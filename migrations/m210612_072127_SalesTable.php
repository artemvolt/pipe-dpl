<?php
declare(strict_types = 1);
use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m210612_072127_SalesTable
 */
class m210612_072127_SalesTable extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('sales', [
			'id' => $this->primaryKey(),
			'product' => $this->integer()->notNull()->comment('Товар'),
			'seller' => $this->integer()->notNull()->comment('Продавец'),
			'create_date' => $this->dateTime()->defaultValue(new Expression('NOW()'))->comment('Дата регистрации'),
			'status' => $this->integer()->null()->comment('Статус'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		$this->createIndex('product', 'sales', 'product', true);
		$this->createIndex('seller', 'sales', 'seller');
		$this->createIndex('status', 'sales', 'status');
		$this->createIndex('deleted', 'sales', 'deleted');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('sales');
	}

}
