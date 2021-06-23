<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210517_114407_rewards
 */
class m210517_114407_rewards extends Migration {
	private const TABLE_NAME = 'reward';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable(self::TABLE_NAME, [
			'id' => $this->primaryKey(),
			'user' => $this->integer()->notNull()->comment('Аккаунт'),
			'operation' => $this->integer()->notNull()->comment('Операция'),
			'rule' => $this->integer()->notNull()->comment('Правило расчёта'),
			'value' => $this->integer()->null()->comment('Расчётное вознаграждение'),
			'comment' => $this->text()->null()->comment('Произвольный комментарий'),
			'create_date' => $this->dateTime()->notNull()->comment('Дата создания'),
			'override' => $this->integer()->null()->comment('Переопределено'),
			'deleted' => $this->boolean()->defaultValue(0)->comment('Флаг удаления')
		]);

		$this->createIndex('user', self::TABLE_NAME, 'user');
		$this->createIndex('operation', self::TABLE_NAME, 'operation');
		$this->createIndex('rule', self::TABLE_NAME, 'rule');
		$this->createIndex('override', self::TABLE_NAME, 'override', true);
		$this->createIndex('deleted', self::TABLE_NAME, 'deleted');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(self::TABLE_NAME);
	}

}
