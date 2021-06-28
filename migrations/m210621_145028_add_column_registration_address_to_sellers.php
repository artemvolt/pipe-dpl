<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210621_145028_add_column_registration_address_to_sellers
 */
class m210621_145028_add_column_registration_address_to_sellers extends Migration {
	private const TABLE = 'sellers';
	private const FIELD = 'reg_address';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropColumn(self::TABLE, self::FIELD);
		$this->addColumn(
			self::TABLE,
			self::FIELD,
			$this->integer()->notNull()->after('passport_when')->comment('Адрес регистрации/проживания')
		);
		$this->createIndex(self::FIELD, self::TABLE, self::FIELD);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex(self::FIELD, self::TABLE);
		$this->dropColumn(self::TABLE, self::FIELD);
		$this->addColumn(
			self::TABLE,
			self::FIELD,
			$this->string()->notNull()->after('passport_when')->comment('Адрес регистрации')
		);
	}

}
