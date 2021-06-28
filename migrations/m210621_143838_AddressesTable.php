<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210621_143838_AddressesTable
 */
class m210621_143838_AddressesTable extends Migration {
	private const TABLE = 'addresses';
	private const INDEXES = [
		'index',
		'area',
		'region',
		'city',
		'street',
		'building'
	];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable(self::TABLE, [
			'id' => $this->primaryKey(),
			'create_date' => $this->dateTime()->notNull()->comment('Дата создания'),
			'index' => $this->integer()->comment('Индекс'),
			'area' => $this->string()->notNull()->comment('Область'),
			'region' => $this->string()->notNull()->comment('Регион/район'),
			'city' => $this->string()->comment('Город/н.п.'),
			'street' => $this->string()->comment('Улица'),
			'building' => $this->string()->comment('Дом'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		foreach (self::INDEXES as $index) {
			$this->createIndex($index, self::TABLE, $index);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(self::TABLE);
	}

}
