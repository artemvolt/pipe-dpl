<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210623_121953_remove_addresses_columns
 */
class m210623_121953_remove_addresses_columns extends Migration {
	private const TABLE = 'sellers';
	private const FIELDS = ['is_resident', 'non_resident_type'];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		foreach (self::FIELDS as $field) {
			$this->dropColumn(self::TABLE, $field);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addColumn(
			self::TABLE,
			self::FIELDS[0],
			$this->boolean()->after('update_date')->notNull()->comment('Резидент'),
		);
		$this->addColumn(
			self::TABLE,
			self::FIELDS[1],
			$this->integer(2)->after('is_resident')->comment('Категория Нерезидента'));

		foreach (self::FIELDS as $field) {
			$this->createIndex($field, self::TABLE, $field);
		}
	}

}
