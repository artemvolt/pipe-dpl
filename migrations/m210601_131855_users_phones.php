<?php
declare(strict_types = 1);
use yii\db\Expression;
use yii\db\Migration;
use yii\db\SqlToken;

/**
 * Class m210601_131855_users_phones
 */
class m210601_131855_users_phones extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('phones', [
			'id' => $this->primaryKey(),
			'phone' => $this->string()->notNull()->comment('Телефон'),
			'create_date' => $this->dateTime()->defaultValue(new Expression('NOW()'))->comment('Дата регистрации'),
			'status' => $this->integer()->null()->comment('Статус'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		$this->createIndex('phone', 'phones', 'phone');
		$this->createIndex('status', 'phones', 'status');
		$this->createIndex('deleted', 'phones', 'deleted');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('phones');
	}

}
