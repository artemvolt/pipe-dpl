<?php
declare(strict_types = 1);

/*При наличии одноимённого файла в подкаталоге /local конфигурация будет взята оттуда*/
if (file_exists($localConfig = __DIR__.DIRECTORY_SEPARATOR.'local'.DIRECTORY_SEPARATOR.basename(__FILE__))) return require $localConfig;

use app\modules\fraud\FraudModule;
use app\modules\recogdol\RecogDolModule;
use yii\console\controllers\MigrateController;
use pozitronik\filestorage\FSModule;
use yii\caching\FileCache;
use yii\log\FileTarget;
use yii\gii\Module as GiiModule;

$params = require __DIR__.'/params.php';
$db = require __DIR__.'/db.php';
$queue = require __DIR__ . '/queue.php';

$config = [
	'id' => 'basic-console',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log', 'queue', 'fraud'],
	'controllerNamespace' => 'app\commands',
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
	],
	'modules' => [
		'fraud' => [
			'class' => FraudModule::class
		],
		'recogdol' => [
			'class' => RecogDolModule::class,
			'params' => [
				'connection' => [
					'host' => '',
					'sslCertificate' => false,
					'user' => '',
					'password' => ''
				]
			]
		],
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
				[
					'class' => FileTarget::class,
					'categories' => ['recogdol.api'],
					'levels' => ['info', 'error'],
					'logFile' => '@runtime/logs/recogdol/api.log',
					'maxFileSize' => 10240
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

if (YII_ENV_DEV) {
	// configuration adjustments for 'dev' environment
	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => GiiModule::class,
	];
}

return $config;
