<?php
declare(strict_types = 1);

use yii\db\Migration;

/**
 * Class m210413_175546_sys_users_restore_pwd
 */
class m210413_175546_sys_users_restore_pwd extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('sys_users', 'restore_code', $this->string('40')->null()->after('salt')->comment('Код восстановления'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('sys_users', 'restore_code');
	}

}
