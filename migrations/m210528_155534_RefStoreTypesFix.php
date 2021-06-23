<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210528_155534_RefStoreTypes
 */
class m210528_155534_RefStoreTypesFix extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->renameTable('ref_store_types', 'ref_stores_types');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->renameTable('ref_stores_types', 'ref_store_types');
	}

}
