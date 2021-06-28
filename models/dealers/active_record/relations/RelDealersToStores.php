<?php
declare(strict_types = 1);

namespace app\models\dealers\active_record\relations;

use pozitronik\relations\traits\RelationsTrait;
use yii\db\ActiveRecord;

/**
 *
 * Связь дилеров с магазинами
 * @property int $id
 * @property int $dealer_id
 * @property int $store_id
 */
class RelDealersToStores extends ActiveRecord {
	use RelationsTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'relation_dealers_to_stores';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['dealer_id', 'store_id'], 'required'],
			[['dealer_id', 'store_id'], 'integer'],
			[['dealer_id', 'store_id'], 'unique', 'targetAttribute' => ['dealer_id', 'store_id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'dealer_id' => 'Dealer ID',
			'store_id' => 'Store ID',
		];
	}
}
