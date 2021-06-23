<?php
declare(strict_types = 1);

namespace app\modules\import\migrations;

use yii\db\Migration;

/**
 * Class m000000_000000_init_import_module
 */
class m000000_000000_init_import_module extends Migration {
	private const TABLE_NAME = 'sys_import';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable(self::TABLE_NAME, [
			'id' => $this->primaryKey(),
			'model' => $this->string()->notNull(),
			'domain' => $this->integer()->notNull(),
			'data' => $this->binary(),
			'processed' => $this->boolean()->defaultValue(false)
		]);

		$this->createIndex('processed', self::TABLE_NAME, 'processed');
		$this->createIndex('domain', self::TABLE_NAME, 'domain');
		$this->createIndex('model', self::TABLE_NAME, 'model');

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(self::TABLE_NAME);

	}
}
