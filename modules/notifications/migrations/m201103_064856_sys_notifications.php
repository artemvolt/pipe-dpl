<?php
declare(strict_types = 1);

namespace app\modules\notifications\migrations;
use app\modules\notifications\models\Notifications;
use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m201103_064856_sys_notifications
 */
class m201103_064856_sys_notifications extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('sys_notifications', [
			'id' => $this->primaryKey(),
			'type' => $this->integer()->notNull()->defaultValue(Notifications::TYPE_DEFAULT)->comment('Тип уведомления'),
			'initiator' => $this->integer()->null()->comment('автор уведомления, null - система'),
			'receiver' => $this->integer()->null()->comment('получатель уведомления, null - определяется типом'),
			'object_id' => $this->integer()->null()->comment('идентификатор объекта уведомления, null - определяется типом'),
			'comment' => $this->text()->null(),
			'timestamp' => $this->dateTime()->defaultValue(new Expression('NOW()'))
		]);

		$this->createIndex('type', 'sys_notifications', 'type');
		$this->createIndex('initiator', 'sys_notifications', 'initiator');
		$this->createIndex('receiver', 'sys_notifications', 'receiver');
		$this->createIndex('object_id', 'sys_notifications', 'object_id');
		$this->createIndex('type_receiver_object_id', 'sys_notifications', ['type', 'receiver', 'object_id'], 'true');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('sys_notifications');
	}

}
