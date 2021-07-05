<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210705_094632_sellers_invite_links
 */
class m210705_094632_sellers_invite_links extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable("sellers_invite_links", [
			'id' => $this->primaryKey(),
			'store_id' => $this->integer()->null(),
			'phone_number' => $this->integer()->null(),
			'email' => $this->string()->null(),
			'token' => $this->string()->notNull(),
			'expired_at' => $this->dateTime()
		]);
		$this->createIndex('idx_store_phone_email', 'sellers_invite_links', [
			'store_id', 'phone_number', 'email'
		], true);
		$this->addForeignKey('fk_invite_link_store', 'sellers_invite_links', 'store_id', 'stores', 'id', "SET NULL", "CASCADE");

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable("sellers_invite_links");
	}

}
