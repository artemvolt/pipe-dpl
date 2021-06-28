<?php
declare(strict_types = 1);

namespace app\models\products\active_record\relations;

use pozitronik\relations\traits\RelationsTrait;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "relation_order_to_product".
 *
 * Связь заказов с товарами
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 */
class RelOrderToProduct extends ActiveRecord {
	use RelationsTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'relation_order_to_product';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['order_id', 'product_id'], 'required'],
			[['order_id', 'product_id'], 'integer'],
			[['order_id', 'product_id'], 'unique', 'targetAttribute' => ['order_id', 'product_id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'order_id' => 'Order ID',
			'product_id' => 'Product ID',
		];
	}
}
