<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210621_080117_DropRefRewardsRules
 */
class m210621_080117_DropRefRewardsRules extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropTable('ref_rewards_rules');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->createTable('ref_rewards_rules', [
			'id' => $this->primaryKey(),
			'name' => $this->string(255)->notNull(),
			'color' => $this->string(255)->null(),
			'textcolor' => $this->string(255)->null(),
			'deleted' => $this->boolean()->notNull()->defaultValue(false)
		]);

		$this->createIndex('deleted', 'ref_rewards_rules', 'deleted');
		$this->createIndex('name', 'ref_rewards_rules', 'name');
	}

}
