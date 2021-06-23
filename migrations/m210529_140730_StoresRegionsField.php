<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210529_140730_StoresRegionsField
 */
class m210529_140730_StoresRegionsField extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('stores', 'region', $this->integer()->notNull()->comment('Филиал')->after('type'));

		$this->createIndex('region', 'stores', 'region');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('stores', 'region');
	}

}
