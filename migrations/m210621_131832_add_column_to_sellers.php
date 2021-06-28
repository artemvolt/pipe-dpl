<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210621_131832_add_column_to_sellers
 */
class m210621_131832_add_column_to_sellers extends Migration {
	private const TABLE = 'sellers';
	private const FIELD = 'citizen';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn(
			self::TABLE,
			self::FIELD,
			$this->integer()->notNull()->after('is_resident')->comment('Гражданство')
		);
		$this->createIndex(self::FIELD, self::TABLE, self::FIELD);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex(self::FIELD, self::TABLE);
		$this->dropColumn(self::TABLE, self::FIELD);
	}

}
