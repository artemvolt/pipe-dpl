<?php
declare(strict_types = 1);

namespace app\models\dealers\active_record\relations;

use pozitronik\relations\traits\RelationsTrait;
use yii\db\ActiveRecord;

/**
 *
 * Связь дилеров с менеджерами
 * @property int $id
 * @property int $dealer_id
 * @property int $manager_id
 */
class RelDealersToManagers extends ActiveRecord {
	use RelationsTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'relation_dealers_to_managers';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['dealer_id', 'manager_id'], 'required'],
			[['dealer_id', 'manager_id'], 'integer'],
			[['dealer_id', 'manager_id'], 'unique', 'targetAttribute' => ['dealer_id', 'manager_id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'dealer_id' => 'Dealer ID',
			'manager_id' => 'Manager ID',
		];
	}
}
