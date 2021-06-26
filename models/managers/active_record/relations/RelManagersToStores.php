<?php
declare(strict_types = 1);

namespace app\models\managers\active_record\relations;

use pozitronik\relations\traits\RelationsTrait;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "relation_managers_to_stores".
 *
 * Связь менеджеров и их торговые точки
 * @property int $id
 * @property int $manager_id
 * @property int $store_id
 */
class RelManagersToStores extends ActiveRecord {
	use RelationsTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'relation_managers_to_stores';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['manager_id', 'store_id'], 'required'],
			[['manager_id', 'store_id'], 'integer'],
			[['manager_id', 'store_id'], 'unique', 'targetAttribute' => ['manager_id', 'store_id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'manager_id' => 'Manager ID',
			'store_id' => 'Store ID',
		];
	}
}
