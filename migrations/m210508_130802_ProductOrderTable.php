<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210508_130802_ProductOrderTable
 */
class m210508_130802_ProductOrderTable extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('product_order', [
			'id' => $this->primaryKey(),
			'initiator' => $this->integer()->notNull()->comment('Заказчик'),
			'store' => $this->integer()->notNull()->comment('Магазин'),
			'status' => $this->integer()->notNull()->comment('Статус'),
			'create_date' => $this->dateTime()->notNull()->comment('Дата регистрации'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		$this->createIndex('initiator', 'product_order', 'initiator');
		$this->createIndex('store', 'product_order', 'store');
		$this->createIndex('status', 'product_order', 'status');
		$this->createIndex('deleted', 'product_order', 'deleted');

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('product_order');
	}

}
