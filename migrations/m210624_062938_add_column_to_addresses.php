<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210624_062938_add_column_to_addresses
 */
class m210624_062938_add_column_to_addresses extends Migration {
	private const TABLE = 'ref_countries';
	private const COLUMN = 'is_homeland';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn(
			self::TABLE,
			self::COLUMN,
			$this->boolean()->defaultValue(false)->comment('Это Россия?')
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn(self::TABLE, self::COLUMN);
	}

}
