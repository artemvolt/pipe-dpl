<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210618_113121_DropRefRewardsOperations
 */
class m210618_113121_DropRefRewardsOperations extends Migration {

	public string $table_name = "ref_rewards_operations";

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropTable($this->table_name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->createTable($this->table_name, [
			'id' => $this->primaryKey(),
			'name' => $this->string(255)->notNull(),
			'color' => $this->string(255)->null(),
			'textcolor' => $this->string(255)->null(),
			'deleted' => $this->boolean()->notNull()->defaultValue(false)
		]);

		$this->createIndex('deleted', $this->table_name, 'deleted');
		$this->createIndex('name', $this->table_name, 'name');
	}

}
