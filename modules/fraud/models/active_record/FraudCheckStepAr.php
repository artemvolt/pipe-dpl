<?php
declare(strict_types = 1);

namespace app\modules\fraud\models\active_record;

use app\components\db\ActiveRecordTrait;
use pozitronik\helpers\DateHelper;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fraud_checks_steps".
 *
 * @property int $id
 * @property int $entity_id ID заказа какой-то сущности
 * @property string $entity_class Класс сущности заказа
 * @property string $fraud_validator Класс фрода, который реализует проверку
 * @property array $step_info Дополнительная информация
 * @property int $status Статус проверки
 * @property string $created_at
 * @property string $updated_at
 */
class FraudCheckStepAr extends ActiveRecord {
	use ActiveRecordTrait;

	/**
	 * @inheritDoc
	 */
	public function behaviors():array {
		return [
			[
				'class' => TimestampBehavior::class,
				'createdAtAttribute' => 'created_at',
				'updatedAtAttribute' => 'updated_at',
				'value' => DateHelper::lcDate(),
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'fraud_checks_steps';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['entity_id', 'status'], 'integer'],
			[['step_info', 'created_at', 'updated_at'], 'safe'],
			[['entity_class', 'fraud_validator'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'entity_id' => 'ID заказа какой-то сущности',
			'entity_class' => 'Класс сущности заказа',
			'fraud_validator' => 'Класс фрода, который реализует проверку',
			'step_info' => 'Дополнительная информация',
			'status' => 'Статус проверки',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}
}
