<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210512_103839_StoresTable
 */
class m210512_103839_StoresTable extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('stores', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->comment('Название магазина'),
			'type' => $this->integer()->notNull()->comment('Тип магазина'),
			'create_date' => $this->dateTime()->notNull()->comment('Дата регистрации'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		$this->createIndex('deleted', 'stores', 'deleted');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('stores');
	}

}
