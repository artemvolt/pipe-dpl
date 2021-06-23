<?php
declare(strict_types = 1);

namespace app\models\dealers\active_record\relations;

use pozitronik\core\traits\Relations;
use yii\db\ActiveRecord;

/**
 *
 * Связь дилеров с продавцами
 * @property int $id
 * @property int $dealer_id
 * @property int $seller_id
 */
class RelDealersToSellers extends ActiveRecord {
	use Relations;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'relation_dealers_to_sellers';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['dealer_id', 'seller_id'], 'required'],
			[['dealer_id', 'seller_id'], 'integer'],
			[['dealer_id', 'seller_id'], 'unique', 'targetAttribute' => ['dealer_id', 'seller_id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'dealer_id' => 'Dealer ID',
			'seller_id' => 'Seller ID',
		];
	}
}
