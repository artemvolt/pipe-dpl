<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210708_072934_email_users_can_default
 */
class m210708_072934_email_users_can_default extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('sys_users', 'email', $this->string()->null());
		$this->alterColumn('sellers', 'name', $this->string()->null());
		$this->alterColumn('sellers', 'surname', $this->string()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('sys_users', 'email', $this->string()->notNull());
		$this->alterColumn('sellers', 'name', $this->string()->notNull());
		$this->alterColumn('sellers', 'surname', $this->string()->notNull());
	}

}
