<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210512_105452_SellersTable
 */
class m210512_105452_SellersTable extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('sellers', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->comment('Имя продавца'),
			'create_date' => $this->dateTime()->notNull()->comment('Дата регистрации'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		$this->createIndex('deleted', 'sellers', 'deleted');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('sellers');
	}

}
