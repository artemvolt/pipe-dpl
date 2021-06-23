<?php
declare(strict_types = 1);

namespace app\modules\status\migrations;

use yii\db\Migration;

/**
 * Class m000000_000000_init_status_module
 * @package app\modules\targets\migrations
 */
class m000000_000000_init_status_module extends Migration {

	private const MAIN_TABLE_NAME = 'sys_status';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable(self::MAIN_TABLE_NAME, [
			'id' => $this->primaryKey(),
			'model_name' => $this->string(255)->null(),
			'model_key' => $this->integer()->null(),
			'status' => $this->integer()->notNull(),
			'at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
			'daddy' => $this->integer()->defaultValue(null),
			'delegate' => $this->string(255)->null(),
		]);

		$this->createIndex('model_name_model_key', self::MAIN_TABLE_NAME, ['model_name', 'model_key'], true);
		$this->createIndex('daddy', self::MAIN_TABLE_NAME, ['daddy']);
		$this->createIndex('status', self::MAIN_TABLE_NAME, ['status']);

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(self::MAIN_TABLE_NAME);
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m200122_082050_sys_targets cannot be reverted.\n";

		return false;
	}
	*/
}
