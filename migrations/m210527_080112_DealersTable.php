<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210527_080112_DealersTable
 */
class m210527_080112_DealersTable extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('dealers', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->comment('Название дилера'),
			'code' => $this->string(4)->notNull()->comment('Код дилера'),
			'client_code' => $this->string(9)->notNull()->comment('Код клиента'),
			'group' => $this->integer()->notNull()->comment('Группа'),
			'branch' => $this->integer()->notNull()->comment('Филиал'),
			'type' => $this->integer()->null()->comment('Тип'),

			'create_date' => $this->dateTime()->notNull()->comment('Дата регистрации'),
			'daddy' => $this->integer()->null()->comment('ID зарегистрировавшего/проверившего пользователя'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		$this->createIndex('deleted', 'dealers', 'deleted');
		$this->createIndex('daddy', 'dealers', 'daddy');
		$this->createIndex('name', 'dealers', 'name');
		$this->createIndex('branch', 'dealers', 'branch');
		$this->createIndex('group', 'dealers', 'group');
		$this->createIndex('type', 'dealers', 'type');
		$this->createIndex('code', 'dealers', 'code', true);
		$this->createIndex('client_code', 'dealers', 'client_code', true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('dealers');
	}

}
