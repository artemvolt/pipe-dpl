<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210617_113536_SetRewardRuleNullable
 */
class m210617_113536_SetRewardRuleNullable extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('rewards', 'rule', $this->integer()->null()->comment('Правило расчёта'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('rewards', 'rule', $this->integer()->notNull()->comment('Правило расчёта'));
	}

}
