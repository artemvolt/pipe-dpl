<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
* Class m210603_090218_add_type_id_to_users_tokens_table
*/
class m210603_090218_add_type_id_to_users_tokens_table extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('sys_users_tokens', 'type_id', $this->tinyInteger()->notNull()->after('auth_token')->comment('Тип токена'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('sys_users_tokens', 'type_id');
	}
}
