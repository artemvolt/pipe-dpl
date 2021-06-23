<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210528_200138_fixRewardRules
 */
class m210528_200138_fixRewardRules extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->renameTable('ref_reward_rule', 'ref_rewards_rules');
		$this->renameTable('ref_reward_operation', 'ref_rewards_operations');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->renameTable('ref_rewards_rules', 'ref_reward_rule');
		$this->renameTable('ref_rewards_operations', 'ref_reward_operation');
	}

}
