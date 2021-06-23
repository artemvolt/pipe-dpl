<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210508_130802_ProductOrderTable
 */
class m210508_131729_ProductTable extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('product', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->comment('Название товара'),
			'item_class' => $this->string()->notNull()->comment('Класс товара'),
			'create_date' => $this->dateTime()->notNull()->comment('Дата регистрации'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		$this->createIndex('deleted', 'product', 'deleted');

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('product');
	}

}
