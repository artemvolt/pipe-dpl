<?php
declare(strict_types = 1);

use yii\db\Migration;

/**
 * Class m210412_114709_sys_users_update_pwd_flag
 */
class m210412_114709_sys_users_update_pwd_flag extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('sys_users', 'is_pwd_outdated', $this->boolean()->notNull()->defaultValue(false)->after('salt')->comment('Ожидается смена пароля'));
		$this->createIndex('is_pwd_outdated', 'sys_users', 'is_pwd_outdated');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('is_pwd_outdated', 'sys_users');
		$this->dropColumn('sys_users', 'is_pwd_outdated');
	}

}
