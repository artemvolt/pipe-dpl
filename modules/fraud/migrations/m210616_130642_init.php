<?php
declare(strict_types = 1);

namespace app\modules\fraud\migrations;

use yii\base\Exception;
use yii\db\Migration;

/**
 * Class m210616_130642_init
 */
class m210616_130642_init extends Migration {
	/**
	 * {@inheritdoc}
	 * @throws Exception
	 */
	public function safeUp() {
		$this->createTable("fraud_checks_steps", [
			'id' => $this->primaryKey(),
			'entity_id' => $this->integer()->comment("ID заказа какой-то сущности"),
			'entity_class' => $this->string()->comment('Класс сущности заказа'),
			'fraud_validator' => $this->string()->comment("Класс фрода, который реализует проверку"),
			'step_info' => $this->json()->comment("Дополнительная информация"),
			'status' => $this->tinyInteger()->comment("Статус проверки"),
			'created_at' => $this->dateTime(),
			'updated_at' => $this->dateTime()
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('fraud_checks_steps');
	}

}
