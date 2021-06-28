<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210623_113753_modify_addresses_column
 */
class m210623_113753_modify_addresses_column extends Migration {
	private const TABLE = 'addresses';
	private const FIELD = 'area';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropColumn(self::TABLE, self::FIELD);

		$this->addColumn(
			self::TABLE,
			self::FIELD,
			$this->integer()->after('index')->comment('Область')
		);

		$this->createIndex(self::FIELD, self::TABLE, self::FIELD);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn(self::TABLE, self::FIELD);

		$this->addColumn(
			self::TABLE,
			self::FIELD,
			$this->string()->after('index')->comment('Область')
		);
		$this->createIndex(self::FIELD, self::TABLE, self::FIELD);
	}

}
