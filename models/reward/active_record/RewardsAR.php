<?php
declare(strict_types = 1);

namespace app\models\reward\active_record;

use app\components\db\ActiveRecordTrait;
use app\models\products\Products;
use app\models\products\ProductsInterface;
use app\models\sys\users\Users;
use app\modules\history\behaviors\HistoryBehavior;
use app\modules\status\models\traits\StatusesTrait;
use Exception;
use pozitronik\helpers\DateHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "reward".
 *
 * @property int $id
 * @property int $user Аккаунт
 * @property int $operation Операция
 * @property int $reason Причина начисления
 * @property null|int $rule Правило расчёта
 * @property int $quantity Расчётное вознаграждение
 * @property string|null $waiting Ожидаемое событие
 * @property string $comment Произвольный комментарий
 * @property int|null $product_id id товара, связанного с вознаграждением (если есть)
 * @property int|null $product_type id типа товара, связанного с вознаграждением (если есть)
 * @property string $create_date Дата создания
 * @property int $override Переопределено
 * @property int $deleted Флаг удаления
 *
 * @property Users $relatedUser Пользователь к которому относится вознаграждение
 */
class RewardsAR extends ActiveRecord {
	use ActiveRecordTrait;
	use StatusesTrait;

	private int $status;//костыль для присвоения статуса новой модели

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'rewards';
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors():array {
		return [
			'history' => [
				'class' => HistoryBehavior::class
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['user', 'operation'], 'required'],
			['create_date', 'default', 'value' => DateHelper::lcDate()],
			[['user', 'operation', 'rule', 'quantity', 'override', 'deleted', 'product_id', 'product_type'], 'integer'],
			[['comment', 'waiting'], 'string'],
			[['create_date'], 'safe'],
			[['override'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'user' => 'Аккаунт',
			'operation' => 'Операция',
			'rule' => 'Правило расчёта',
			'reason' => 'Причина начисления',
			'quantity' => 'Расчётное вознаграждение',
			'comment' => 'Произвольный комментарий',
			'create_date' => 'Дата создания',
			'override' => 'Переопределено',
			'deleted' => 'Флаг удаления',
			'currentStatus' => 'Статус вознаграждения',
			'refRewardsRules' => 'Правило расчёта',
			'relatedUser' => 'Пользователь',
			'waiting' => 'Ожидаемое событие'
		];
	}

	/**
	 * @return ProductsInterface|null
	 * @throws Exception
	 */
	public function getRelatedProducts():?ProductsInterface {
		return Products::getModel($this->product_id, $this->product_type);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedUser():ActiveQuery {
		return $this->hasOne(Users::class, ['id' => 'user']);
	}

	/**
	 * @param mixed $relatedUser
	 */
	public function setRelatedUser($relatedUser):void {
		if (null !== $user = self::ensureModel(Users::class, $relatedUser)) {
			/** @var Users $user */
			$this->user = $user->id;
		}
	}

	/**
	 * @return int
	 */
	public function getStatus():int {
		return $this->currentStatusId;
	}

	/**
	 * @param int $status
	 */
	public function setStatus(int $status):void {
		if ($this->isNewRecord) {
			$this->on(ActiveRecord::EVENT_AFTER_INSERT, function($event) {//отложим связывание после сохранения
				$this->currentStatusId = $event->data[0];
			}, [$status]);
		}
	}

}
