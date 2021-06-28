<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210621_153758_modify_column_registration_address_to_sellers
 */
class m210621_153758_modify_column_registration_address_to_sellers extends Migration {
	private const TABLE = 'sellers';
	private const FIELD = 'reg_address';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn(
			self::TABLE,
			self::FIELD,
			$this->integer()->after('passport_when')->comment('Адрес регистрации/проживания')
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn(
			self::TABLE,
			self::FIELD,
			$this->integer()->notNull()->after('passport_when')->comment('Адрес регистрации/проживания')
		);
	}

}
