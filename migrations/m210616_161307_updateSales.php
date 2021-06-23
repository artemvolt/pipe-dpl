<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210616_161307_updateSales
 */
class m210616_161307_updateSales extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropIndex('product', 'sales');
		$this->renameColumn('sales', 'product', 'product_id');
		$this->addColumn('sales', 'product_type', $this->integer()->notNull()->after('product_id'));
		$this->createIndex('product_id_product_type', 'sales', ['product_id', 'product_type'], true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('product_id_product_type', 'sales');
		$this->dropColumn('sales', 'product_type');
		$this->renameColumn('sales', 'product_id', 'product');
		$this->createIndex('product', 'sales', 'product', true);
	}

}
