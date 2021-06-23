<?php
declare(strict_types = 1);

use yii\db\Migration;

/**
 * Class m210406_063557_sys_permissions
 */
class m210406_063557_sys_permissions extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('sys_permissions', [
			'id' => $this->primaryKey(),
			'name' => $this->string(128)->notNull()->comment('Название доступа'),
			'controller' => $this->string()->null()->comment('Контроллер, к которому устанавливается доступ, null для внутреннего доступа'),
			'action' => $this->string()->null()->comment('Действие, для которого устанавливается доступ, null для всех действий контроллера'),
			'verb' => $this->string()->null()->comment('REST-метод, для которого устанавливается доступ'),
			'comment' => $this->text()->null()->comment('Описание доступа'),
			'priority' => $this->integer()->notNull()->defaultValue(0)->comment('Приоритет использования (больше - выше)')
		]);

		$this->createIndex('controller_action_verb', 'sys_permissions', ['controller', 'action', 'verb']);
		$this->createIndex('priority', 'sys_permissions', ['priority']);
		$this->createIndex('name', 'sys_permissions', ['name'], true);

		$this->createTable('sys_relation_users_to_permissions', [
			'id' => $this->primaryKey(),
			'user_id' => $this->integer()->notNull()->comment('Ключ объекта доступа'),
			'permission_id' => $this->integer()->notNull()->comment('Ключ правила доступа'),
		]);

		$this->createIndex('user_id_permission_id', 'sys_relation_users_to_permissions', ['user_id', 'permission_id'], true);

		$this->createTable('sys_permissions_collections', [
			'id' => $this->primaryKey(),
			'name' => $this->string(128)->notNull()->comment('Название группы доступа'),
			'comment' => $this->text()->null()->comment('Описание группы доступа')
		]);

		$this->createIndex('name', 'sys_permissions_collections', ['name'], true);

		$this->createTable('sys_relation_permissions_collections_to_permissions', [
			'id' => $this->primaryKey(),
			'collection_id' => $this->integer()->notNull()->comment('Ключ группы доступа'),
			'permission_id' => $this->integer()->notNull()->comment('Ключ правила доступа'),
		]);

		$this->createIndex('collection_id_permission_id', 'sys_relation_permissions_collections_to_permissions', ['collection_id', 'permission_id'], true);

		$this->createTable('sys_relation_users_to_permissions_collections', [
			'id' => $this->primaryKey(),
			'user_id' => $this->integer()->notNull()->comment('Ключ объекта доступа'),
			'collection_id' => $this->integer()->notNull()->comment('Ключ группы доступа'),
		]);

		$this->createIndex('user_id_collection_id', 'sys_relation_users_to_permissions_collections', ['user_id', 'collection_id'], true);

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('sys_permissions');
		$this->dropTable('sys_relation_users_to_permissions');
		$this->dropTable('sys_permissions_collections');
		$this->dropTable('sys_relation_permissions_collections_to_permissions');
		$this->dropTable('sys_relation_users_to_permissions_collections');
	}

}
