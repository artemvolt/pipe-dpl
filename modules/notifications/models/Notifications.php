<?php
declare(strict_types = 1);

namespace app\modules\notifications\models;

use app\models\sys\users\Users;
use pozitronik\core\traits\ARExtended;
use pozitronik\helpers\ArrayHelper;
use Throwable;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\ForbiddenHttpException;

/**
 * Потенциальная проблема с уведомлениями: при вставке пользователей в TargetUserAssignedNotificationBehavior на каждого пользователя будет происходить один вызов. Если пользователей много -- это будут тормоза вечности.
 * Исправить это можно, убрав вставку в поведении, и добавив её в Targets::setRelLevelUsers. Тогда вызов произойдёт на весь массив скопом. Но отслеживать такое сложнее, и архитектурно это не совсем правильно.
 * Второй способ -- реализовать асинхронную вставку.
 * Но поскольку описанная ситуация больше относится к гипотетической (сначала интерпретатор умрёт от превышения max_input_vars), всё оставлено как есть (до возникновения проблемы).
 *
 * С другими поведениями проблемы могут быть аналогичными, но там данных всё-таки меньше.
 *
 *
 * @property int $id
 * @property int $type Тип уведомления
 * @property null|int $initiator Автор уведомления, null - система
 * @property null|int $receiver Получатель уведомления, null - определяется типом
 * @property null|int $object_id Идентификатор объекта уведомления, null - определяется типом
 * @property null|string $comment Свободный комментарий уведомления
 * @property string $timestamp Таймстамп создания уведомления
 *
 * @property null|Users $relatedReceiver Пользователь, получающий оповещение
 * @property null|Users $relInitiator Пользователь, создавший оповещение
 * @property string $message Сообщение события
 */
class Notifications extends ActiveRecord {
	use ARExtended;

	/*Константы типов*/
	public const TYPE_DEFAULT = 0;//уведомление без особого типа

	public const NOTIFICATIONS_TYPES = [

	];

	/**
	 * @inheritdoc
	 */
	public static function tableName():string {
		return 'sys_notifications';
	}

	/**
	 * @inheritdoc
	 */
	public function rules():array {
		return [
			[['type', 'initiator', 'receiver', 'object_id'], 'integer'],
			[['type'], 'required'],
			[['comment', 'timestamp'], 'safe'],
			['type', 'default', 'value' => self::TYPE_DEFAULT],
			[['type', 'receiver', 'object_id'], 'unique', 'targetAttribute' => ['type', 'receiver', 'object_id'], 'message' => 'Notification already posted']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'type' => 'Тип',
			'initiator' => 'Инициатор',
			'receiver' => 'Получатель',
			'object_id' => 'Идентификатор объекта',
			'message' => 'Сообщение',
			'comment' => 'Описание',
			'timestamp' => 'Время создания'
		];
	}

	/**
	 * @return string
	 * @throws Throwable
	 */
	public function getMessage():string {
		if (self::TYPE_DEFAULT === $this->type) return $this->comment;

		$message = ArrayHelper::getValue(self::NOTIFICATIONS_TYPES, $this->type, '');
		if ('' !== $this->message) {
			$message .= (null === $this->initiator)
				?" (система)"
				:" пользователем {$this->getInitiatorName($this->initiator)}";
		}
		return $message;
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedReceiver():?ActiveQuery {
		return $this->hasOne(Users::class, ['id' => 'receiver']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedInitiator():?ActiveQuery {
		return $this->hasOne(Users::class, ['id' => 'initiator']);
	}

	/**
	 * @param int|null $id
	 * @return string
	 */
	private function getInitiatorName(?int $id):string {
		if (null === $id) return '';
		return Yii::$app->cache->getOrSet("Notifications::InitiatorName($id)", static function() use ($id) {
			return Users::findOne($id)->username;
		});
	}

	/**
	 * Отправка дефолтного текстового сообщения
	 * @param string $message
	 * @param null|int|int[] $receivers
	 * @param int|null $initiator
	 * @throws Exception
	 * @throws ForbiddenHttpException
	 */
	public static function message(string $message, $receivers = null, ?int $initiator = null):void {
		if (null === $receivers) $receivers = Users::Current()->id;
		$receivers = (array)$receivers;
		$insertData = [];
		foreach ($receivers as $receiver) {
			$insertData[] = [
				'type' => self::TYPE_DEFAULT,
				'initiator' => $initiator,
				'receiver' => $receiver,
				'comment' => $message
			];
		}
		if ([] !== $insertData) {
			Yii::$app->db->createCommand(Yii::$app->db->createCommand()
					->batchInsert(self::tableName(), ['type', 'initiator', 'receiver', 'comment'], $insertData)
					->rawSql." ON DUPLICATE KEY UPDATE `id` = `id`")
				->execute();
		}
	}

	/**
	 * Отправить оповещение типа $type, связанное с объектом $object получателям $receivers
	 * @param int|null $object
	 * @param int[] $receivers
	 * @param null|int $initiator
	 * @param int $type
	 * @param null|string $comment
	 * @throws Exception
	 */
	public static function push(?int $object, array $receivers, ?int $initiator = null, int $type = Notifications::TYPE_DEFAULT, ?string $comment = null):void {
		$insertData = [];
		$receivers = array_filter(array_unique($receivers), static function($value, $key) use ($initiator) {//инициатора уведомлять не нужно
			return (!empty($value) && $initiator !== (int)$value);
		}, ARRAY_FILTER_USE_BOTH);
		foreach ($receivers as $receiver) {
			$insertData[] = [
				'type' => $type,
				'initiator' => $initiator,
				'receiver' => $receiver,
				'object_id' => $object,
				'comment' => $comment
			];
		}
		if ([] !== $insertData) {
			Yii::$app->db->createCommand(Yii::$app->db->createCommand()
					->batchInsert(self::tableName(), ['type', 'initiator', 'receiver', 'object_id', 'comment'], $insertData)
					->rawSql." ON DUPLICATE KEY UPDATE `id` = `id`")
				->execute();//угу, официальный upsert работает так
		}
	}

	/**
	 * Очистка уведомления
	 * @param int|null $object объект уведомления
	 * @param int|null $receiver получатель уведомления, null - текущий пользователь
	 * @param int[]|null $type null - очищает все уведомления, связанные с целью у пользователя
	 * @throws ForbiddenHttpException
	 */
	public static function Acknowledge(?int $object, ?int $receiver = null, ?array $type = null):void {
		$dropCondition = [
			'receiver' => $receiver??Users::Current()->id,
			'type' => $type??self::TYPE_DEFAULT
		];

		if (null !== $object) $dropCondition['object_id'] = $object;

		self::deleteAll($dropCondition);
	}

	/**
	 * Возвращает все уведомления о заданной цели для заданного/текущего пользователя
	 * @param int $object
	 * @param int|null $receiver получатель уведомления, null - текущий пользователь
	 * @return self[]
	 * @throws ForbiddenHttpException
	 */
	public static function Notifications(int $object, ?int $receiver = null):array {
		if (null === $receiver) $receiver = Users::Current()->id;
		return self::find()->where(['object_id' => $object, 'receiver' => $receiver, 'type' => self::TYPE_DEFAULT])->all();
	}

	/**
	 * Возвращает все уведомления текущего пользователя
	 * @param int|null $receiver получатель уведомления, null - текущий пользователь
	 * @return self[]
	 * @throws ForbiddenHttpException
	 */
	public static function UserNotifications(?int $receiver = null):array {
		if (null === $receiver) $receiver = Users::Current()->id;
		return self::find()->where(['receiver' => $receiver, 'type' => self::TYPE_DEFAULT])->all();
	}

}
