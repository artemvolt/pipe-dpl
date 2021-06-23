<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210508_132328_RelOrderProduct
 */
class m210508_132328_RelOrderProduct extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('relation_order_to_product', [
			'id' => $this->primaryKey(),
			'order_id' => $this->integer()->notNull(),
			'product_id' => $this->integer()->notNull(),
		]);

		$this->createIndex('order_id_product_id', 'relation_order_to_product', ['order_id', 'product_id'], true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('relation_order_to_product');
	}

}
