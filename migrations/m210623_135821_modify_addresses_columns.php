<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210623_135821_modify_addresses_columns
 */
class m210623_135821_modify_addresses_columns extends Migration {
	private const TABLE = 'addresses';
	private const COLUMN = 'index';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn(
			self::TABLE,
			self::COLUMN,
			$this->integer()->comment('Индекс')
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn(
			self::TABLE,
			self::COLUMN,
			$this->integer()->notNull()->comment('Индекс')
		);
	}

}
