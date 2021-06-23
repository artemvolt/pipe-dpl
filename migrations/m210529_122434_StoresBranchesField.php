<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210529_122434_StoresBranchesField
 */
class m210529_122434_StoresBranchesField extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('stores', 'branch', $this->integer()->notNull()->comment('Филиал')->after('type'));

		$this->createIndex('branch', 'stores', 'branch');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('stores', 'branch');
	}

}
