<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
* Class m210528_161326_StoresSellerChannel
*/
class m210528_161326_StoresSellerChannel extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('stores', 'selling_channel', $this->integer()->notNull()->comment('Канал продаж')->after('type'));

		$this->createIndex('selling_channel', 'stores', 'selling_channel');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('stores', 'selling_channel');
	}

}
