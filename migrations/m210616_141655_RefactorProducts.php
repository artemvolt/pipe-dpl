<?php
declare(strict_types = 1);
use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m210616_141655_RefactorProducts
 */
class m210616_141655_RefactorProducts extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->renameTable('product', 'products_classes');
		$this->createTable('products', [
			'id' => $this->primaryKey(),
			'class_id' => $this->integer()->notNull()->comment('Класс продукта'),
			'user' => $this->integer()->comment('Пользователь'),
			'create_date' => $this->dateTime()->defaultValue(new Expression('NOW()'))->comment('Дата создания'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		$this->createIndex('user', 'products', 'user');
		$this->createIndex('deleted', 'products', 'deleted');
		$this->createIndex('class_id', 'products', 'class_id', 'true');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('products');
		$this->renameTable('products_classes', 'product');
	}

}
