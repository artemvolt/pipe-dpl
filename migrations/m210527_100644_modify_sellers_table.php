<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210527_100644_modify_sellers_table
 */
class m210527_100644_modify_sellers_table extends Migration {
	private const INDEXES = [
		'name',
		'surname',
		'patronymic',
		'birthday',
		'gender',
		'entry_date',
		'inn',
		'snils',
		'keyword',
		'user',
		'create_date',
		'update_date',
		'sale_point',
		'dealer'
	];

	private const UNIQUE_INDEXES = [
		'inn',
		'snils',
		'user'
	];

	private const COMPLEX_INDEX = [
		['passport_series', 'passport_number']
	];

	private const TABLE = 'sellers';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropTable(self::TABLE);

		$this->createTable(self::TABLE, [
			'id' => $this->primaryKey(),
			'user' => $this->integer()->comment('Пользователь'),
			'name' => $this->string(128)->notNull()->comment('Имя'),
			'surname' => $this->string(128)->notNull()->comment('Фамилия'),
			'patronymic' => $this->string(128)->comment('Отчество'),
			'gender' => $this->integer()->comment('Пол'),
			'birthday' => $this->date()->notNull()->comment('Дата рождения'),
			'create_date' => $this->dateTime()->notNull()->comment('Дата регистрации'),
			'update_date' => $this->dateTime()->comment('Дата обновления'),
			'is_resident' => $this->boolean()->notNull()->comment('Резидент'),
			'passport_series' => $this->string(64)->notNull()->comment('Серия паспорта'),
			'passport_number' => $this->string(64)->notNull()->comment('Номер паспорта'),
			'passport_whom' => $this->string()->notNull()->comment('Кем выдан паспорт'),
			'passport_when' => $this->date()->notNull()->comment('Когда выдан паспорт'),
			'reg_address' => $this->string()->notNull()->comment('Адрес регистрации'),
			'entry_date' => $this->date()->comment('Дата въезда в страну'),
			'inn' => $this->string(12)->comment('ИНН'),
			'snils' => $this->string(14)->comment('СНИЛС'),
			'keyword' => $this->string(64)->notNull()->comment('Ключевое слово для  «Горячей линии»'),
			'is_wireman_shpd' => $this->boolean()->notNull()->comment('Монтажник ШПД'),
			'dealer' => $this->integer()->comment('Дилер'),
			'sale_point' => $this->integer()->comment('Торговая точка'),
			'contract_signing_address' => $this->string()->comment('Адрес подписания договора'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		foreach (self::INDEXES as $index) {
			$this->createIndex($index, self::TABLE, $index, in_array($index, self::UNIQUE_INDEXES));
		}

		foreach (self::COMPLEX_INDEX as $index) {
			$this->createIndex(implode('_', $index), self::TABLE, $index, true);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(self::TABLE);

		$this->createTable(self::TABLE, [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->comment('Имя продавца'),
			'create_date' => $this->dateTime()->notNull()->comment('Дата регистрации'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		$this->createIndex('deleted', self::TABLE, 'deleted');
	}

}
