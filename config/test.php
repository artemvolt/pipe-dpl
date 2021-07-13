<?php
declare(strict_types = 1);

use app\models\tests\MemoryQueue;
use yii\caching\DummyCache;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\web\AssetManager;

/** @noinspection UsingInclusionReturnValueInspection */
$web = require __DIR__.'/web.php';

return ArrayHelper::merge(
	$web,
	[
		'components' => [
			'db' => [
				'class' => Connection::class,
				'dsn' => getenv('TEST_DB_DSN'),
				'username' => getenv('TEST_DB_USER'),
				'password' => getenv('TEST_DB_PASS'),
				'enableSchemaCache' => false,
			],
			'cache' => [
				'class' => DummyCache::class
			],
			'assetManager' => [
				'class' => AssetManager::class,
				'basePath' => '@webroot/web/assets',
			],
			'queue' => [
				'class' => MemoryQueue::class,
			],
		]
	]
);
