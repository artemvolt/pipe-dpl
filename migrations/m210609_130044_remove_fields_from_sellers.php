<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210609_130044_remove_fields_from_sellers
 */
class m210609_130044_remove_fields_from_sellers extends Migration {
	private const TABLE = 'sellers';
	private const INDEXES = [
		'sale_point',
		'dealer'
	];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropColumn(self::TABLE, 'dealer');
		$this->dropColumn(self::TABLE, 'sale_point');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addColumn(self::TABLE, 'dealer', $this->integer()->comment('Дилер'));
		$this->addColumn(self::TABLE, 'sale_point', $this->integer()->comment('Торговая точка'));

		foreach (self::INDEXES as $index) {
			$this->createIndex($index, self::TABLE, $index);
		}
	}

}
