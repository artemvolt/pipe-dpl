<?php
declare(strict_types = 1);

use yii\db\Migration;

/**
 * Class m180930_093408_sys_users
 */
class m180930_093408_sys_users extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('sys_users', [
			'id' => $this->primaryKey(),
			'username' => $this->string(255)->notNull()->comment('Отображаемое имя пользователя'),
			'login' => $this->string(64)->notNull()->comment('Логин'),
			'password' => $this->string(255)->notNull()->comment('Хеш пароля'),
			'salt' => $this->string(255)->null()->comment('Unique random salt hash'),
			'email' => $this->string(255)->notNull()->comment('email'),
			'comment' => $this->text()->null()->comment('Служебный комментарий пользователя'),
			'create_date' => $this->dateTime()->notNull()->comment('Дата регистрации'),
			'daddy' => $this->integer()->null()->comment('ID зарегистрировавшего/проверившего пользователя'),
			'deleted' => $this->boolean()->defaultValue(0)->comment('Флаг удаления')
		]);

		$this->createIndex('username', 'sys_users', 'username');
		$this->createIndex('login', 'sys_users', 'login', true);
		$this->createIndex('email', 'sys_users', 'email', true);
		$this->createIndex('daddy', 'sys_users', 'daddy');
		$this->createIndex('deleted', 'sys_users', 'deleted');

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('sys_users');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m180930_093408_sys_users cannot be reverted.\n";

		return false;
	}
	*/
}
