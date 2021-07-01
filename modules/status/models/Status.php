<?php
declare(strict_types = 1);

namespace app\modules\status\models;

use app\components\db\ActiveRecordTrait;
use app\models\sys\users\Users;
use ReflectionClass;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "sys_status".
 *
 * @property int $id
 * @property string $model_name
 * @property int $model_key
 * @property int $status
 * @property string $at
 * @property int $daddy
 * @property string $delegate
 */
class Status extends ActiveRecord {
	use ActiveRecordTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'sys_status';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['model_key', 'status', 'daddy', 'delegate'], 'integer'],
			[['status'], 'required'],
			[['at'], 'safe'],
			[['model_name'], 'string', 'max' => 255],
			[['model_name', 'model_key'], 'unique', 'targetAttribute' => ['model_name', 'model_key']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'model_name' => 'Model Name',
			'model_key' => 'Model Key',
			'status' => 'Status',
			'at' => 'At',
			'daddy' => 'Daddy',
			'delegate' => 'Delegate',
		];
	}

	/**
	 * Разлагаем на плесень и на липовый мёд
	 * @param ActiveRecord $model
	 * @return array
	 * @throws InvalidConfigException
	 */
	private static function ExtractModelIdentifiers(ActiveRecord $model):array {
		$reflector = new ReflectionClass($model);
		if (is_array($pk = $model->primaryKey)) throw new InvalidConfigException('Составные ключи не поддерживаются');
		if (null === $pk) throw new InvalidConfigException('У связанной модели должен быть указан первичный ключ');
		return [
			'model_name' => $reflector->name,
			'model_key' => $pk
		];
	}

	/**
	 * @param ActiveRecord $model
	 * @return int|null
	 * @throws InvalidConfigException
	 */
	public static function getCurrentStatus(ActiveRecord $model):?int {
		if (null === $currentStatus = self::find()->where(self::ExtractModelIdentifiers($model))->one()) return null;/*запись не найдена*/
		/** @var self $currentStatus */
		return $currentStatus->status;
	}

	/**
	 * @param ActiveRecord $model
	 * @param int $status
	 * @return bool
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws InvalidConfigException
	 */
	public static function setCurrentStatus(ActiveRecord $model, int $status):bool {
		$attributes = self::ExtractModelIdentifiers($model);
		$currentStatus = self::getInstance($attributes);
		if ($currentStatus->isNewRecord) {
			$currentStatus->load($attributes, '');
		}
		$currentStatus->status = $status;
		$currentStatus->daddy = !Yii::$app->user->isGuest?Users::Current()->id:null;
		/* не используется, осталось из TWS, может быть появится и у нас такой
		 * $currentStatus->delegate = (false === $delegateId = Yii::$app->cache->get(Delegation::RELOGIN_PREFIX.Yii::$app->user->id))?null:$delegateId;*/
		return ($currentStatus->isNewRecord)?$currentStatus->save():(1 === $currentStatus->update());
	}
}
