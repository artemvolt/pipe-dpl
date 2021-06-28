<?php
declare(strict_types = 1);
use app\models\seller\Sellers;
use yii\db\Migration;

/**
 * Class m210625_115616_modify_sellers_field_citizen
 */
class m210625_115616_modify_sellers_field_citizen extends Migration {
	private const TABLE = 'sellers';
	private const FIELD = 'citizen';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn(
			self::TABLE,
			self::FIELD,
			$this->integer()->defaultValue(null)->after('update_date')->comment('Гражданство')
		);

		Sellers::updateAll([self::FIELD => null], [self::FIELD => 0]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		Sellers::updateAll([self::FIELD => 0], [self::FIELD => null]);

		$this->alterColumn(
			self::TABLE,
			self::FIELD,
			$this->integer()->notNull()->after('update_date')->comment('Гражданство')
		);
	}

}
