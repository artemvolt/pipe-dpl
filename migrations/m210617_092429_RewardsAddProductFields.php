<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210617_092429_RewardsAddProductFields
 */
class m210617_092429_RewardsAddProductFields extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('rewards', 'product_id', $this->integer()->null()->after('comment'));
		$this->addColumn('rewards', 'product_type', $this->integer()->null()->after('product_id'));

		$this->createIndex('product_id_product_type', 'rewards', ['product_id', 'product_type']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('rewards', 'product_id');
		$this->dropColumn('rewards', 'product_type');

		$this->dropIndex('product_id_product_type', 'rewards');
	}

}
