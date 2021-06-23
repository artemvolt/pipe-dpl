<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210610_064419_ManagerTable
 */
class m210610_064419_ManagerTable extends Migration {
	private const TABLE = 'managers';
	private const INDEXES = [
		'name',
		'surname',
		'patronymic'
	];

	private const UNIQUE_INDEXES = [
		'user'
	];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('managers', [
			'id' => $this->primaryKey(),
			'create_date' => $this->dateTime()->notNull()->comment('Дата регистрации'),
			'update_date' => $this->dateTime()->comment('Дата обновления'),
			'user' => $this->integer()->comment('Пользователь'),
			'name' => $this->string(128)->notNull()->comment('Имя'),
			'surname' => $this->string(128)->notNull()->comment('Фамилия'),
			'patronymic' => $this->string(128)->comment('Отчество'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		foreach (self::INDEXES as $index) {
			$this->createIndex($index, self::TABLE, $index, in_array($index, self::UNIQUE_INDEXES));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('managers');
	}

}
