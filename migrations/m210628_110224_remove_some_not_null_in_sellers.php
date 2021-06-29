<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210628_110224_remove_some_not_null_in_sellers
 */
class m210628_110224_remove_some_not_null_in_sellers extends Migration {
	private const TABLE = 'sellers';
	private const FIELDS = [
		'birthday',
		'passport_series',
		'passport_number',
		'passport_whom',
		'passport_when',
		'keyword'
	];

	private const COMPLEX_INDEX = [
		['passport_series', 'passport_number']
	];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[0],
			$this->date()->comment('Дата рождения')
		);
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[1],
			$this->string(64)->comment('Серия паспорта')
		);
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[2],
			$this->string(64)->comment('Номер паспорта')
		);
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[3],
			$this->string()->comment('Кем выдан паспорт')

		);
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[4],
			$this->date()->comment('Когда выдан паспорт')
		);
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[5],
			$this->string(64)->comment('Ключевое слово для  «Горячей линии»')
		);

		foreach (self::COMPLEX_INDEX as $index) {
			$this->dropIndex(implode('_', $index), self::TABLE);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[0],
			$this->date()->notNull()->comment('Дата рождения')
		);
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[1],
			$this->string(64)->notNull()->comment('Серия паспорта')
		);
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[2],
			$this->string(64)->notNull()->comment('Номер паспорта')
		);
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[3],
			$this->string()->notNull()->comment('Кем выдан паспорт')

		);
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[4],
			$this->date()->notNull()->comment('Когда выдан паспорт')
		);
		$this->alterColumn(
			self::TABLE,
			self::FIELDS[5],
			$this->string(64)->notNull()->comment('Ключевое слово для  «Горячей линии»')
		);

		foreach (self::COMPLEX_INDEX as $index) {
			$this->createIndex(implode('_', $index), self::TABLE, $index, true);
		}
	}

}
