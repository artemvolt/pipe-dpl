<?php
declare(strict_types = 1);

use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m210417_083359_sys_users_auth_tokens
 */
class m210417_083359_sys_users_auth_tokens extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('sys_users_tokens', [
			'id' => $this->primaryKey(),
			'user_id' => $this->integer()->notNull()->comment('user id foreign key'),
			'auth_token' => $this->string(40)->notNull()->comment('Bearer auth token'),
			'created' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP'))->notNull()->comment('Таймстамп создания'),
			'valid' => $this->timestamp()->null()->comment('Действует до'),
			'ip' => $this->string(255)->null()->comment('Адрес авторизации'),
			'user_agent' => $this->string(255)->null()->comment('User-Agent')
		]);

		$this->createIndex('user_id_auth_token', 'sys_users_tokens', ['user_id', 'auth_token'], true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('user_id_auth_token', 'sys_users_tokens');
		$this->dropTable('sys_users_tokens');
	}

}
