<?php
declare(strict_types = 1);

use yii\console\controllers\MigrateController;
use pozitronik\filestorage\FSModule;
use yii\caching\FileCache;
use yii\log\FileTarget;

$params = require __DIR__.'/params.php';
$db = require __DIR__.'/db.php';
$queue = require __DIR__ . '/queue.php';

$config = [
	'id' => 'basic-console',
	'basePath' => dirname(__DIR__, 2),
	'bootstrap' => ['log', 'queue'],
	'controllerNamespace' => 'app\commands',
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
	],
	'modules' => [
//		'fraud' => [
//			'class' => FraudModule::class
//		],
		'filestorage' => [
			'class' => FSModule::class,
			'defaultRoute' => 'index',
			'params' => [
				'tableName' => 'sys_file_storage',//используемая таблица хранения метаданных
				'tableNameTags' => 'sys_file_storage_tags',//используемая таблица хранения тегов
				'base_dir' => '@app/web/uploads/',//каталог хранения файлов
				'models_subdirs' => true,//файлы каждой модели кладутся в подкаталог с именем модели
				'name_subdirs_length' => 2//если больше 0, то файлы загружаются в подкаталоги по именам файлов (параметр регулирует длину имени подкаталогов)
			]
		],
	],
	'components' => [
		'cache' => [
			'class' => FileCache::class,
		],
		'log' => [
			'targets' => [
				[
					'class' => FileTarget::class,
					'levels' => ['error', 'warning'],
				],
			],
		],
		'permissions' => require __DIR__.'/permissions.php',
		'db' => $db,
		'queue' => $queue
	],
	'controllerMap' => [
		'migrate' => [
			'class' => MigrateController::class,
			'templateFile' => '@app/migrations/template/default_migration_template.php',
			'migrationNamespaces' => [
				'app\modules\history\migrations',// <== именно неймспейс, не путь
				'app\modules\status\migrations',
				'app\modules\import\migrations',
				'app\modules\notifications\migrations',
				'app\modules\fraud\migrations',
			],
		],
	],

	'params' => $params,
];

return $config;
