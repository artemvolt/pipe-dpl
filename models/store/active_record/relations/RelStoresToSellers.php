<?php
declare(strict_types = 1);

namespace app\models\store\active_record\relations;

use pozitronik\relations\traits\RelationsTrait;
use yii\db\ActiveRecord;

/**
 *
 * Связь магазинов с продавцами
 * @property int $id
 * @property int $store_id
 * @property int $seller_id
 */
class RelStoresToSellers extends ActiveRecord {
	use RelationsTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'relation_stores_to_sellers';
	}

	/**
	 * @param int $storeId
	 * @param int $sellerId
	 * @return static
	 */
	public static function assign(int $storeId, int $sellerId):self {
		$self = new self();
		$self->store_id = $storeId;
		$self->seller_id = $sellerId;
		return $self;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['store_id', 'seller_id'], 'required'],
			[['store_id', 'seller_id'], 'integer'],
			[['store_id', 'seller_id'], 'unique', 'targetAttribute' => ['store_id', 'seller_id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'store_id' => 'Store ID',
			'seller_id' => 'Seller ID',
		];
	}
}
