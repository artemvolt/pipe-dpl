<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210604_123308_add_field_noresident_category_to_sellers
 */
class m210604_123308_add_field_noresident_category_to_sellers extends Migration {
	private const TABLE = 'sellers';
	private const COLUMN_ADD = 'non_resident_type';
	private const COLUMN_MODIFY = 'gender';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn(
			self::TABLE,
			self::COLUMN_ADD,
			$this->integer(2)->after('is_resident')->comment('Категория Нерезидента')
		);
		$this->alterColumn(
			self::TABLE,
			self::COLUMN_MODIFY,
			$this->integer(2)->comment('Пол')
		);

		$this->createIndex(self::COLUMN_ADD, self::TABLE, self::COLUMN_ADD);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn(self::TABLE, self::COLUMN_ADD);
		$this->alterColumn(
			self::TABLE,
			self::COLUMN_MODIFY,
			$this->integer()->comment('Пол')
		);
	}

}
