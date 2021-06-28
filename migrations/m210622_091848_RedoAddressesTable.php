<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
* Class m210622_091848_RedoAddressesTable
*/
class m210622_091848_RedoAddressesTable extends Migration {
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
		$this->dropTable(self::TABLE);

		$this->createTable(self::TABLE, [
			'id' => $this->primaryKey(),
			'create_date' => $this->dateTime()->notNull()->comment('Дата создания'),
			'index' => $this->integer()->notNull()->comment('Индекс'),
			'area' => $this->string()->comment('Область'),
			'region' => $this->string()->comment('Регион/район'),
			'city' => $this->string()->notNull()->comment('Город/н.п.'),
			'street' => $this->string()->notNull()->comment('Улица'),
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

}
