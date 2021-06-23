<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210612_083635_ModifyRewardTable
 */
class m210612_083635_ModifyRewardTable extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->renameTable('reward', 'rewards');
		$this->addColumn('rewards', 'reason', $this->integer()->notNull()->comment('Причина начисления')->after('operation'));

		$this->renameColumn('rewards', 'value', 'quantity');
		$this->addColumn('rewards', 'waiting', $this->string()->notNull()->comment('Ожидает события')->after('quantity'));

		$this->createIndex('reason', 'rewards', 'reason');
		$this->createIndex('waiting', 'rewards', 'waiting');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('rewards', 'waiting');
		$this->dropColumn('rewards', 'reason');
		$this->renameColumn('rewards', 'quantity', 'value');
		$this->renameTable('rewards', 'reward');
	}

}
