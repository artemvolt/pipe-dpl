<?php
declare(strict_types = 1);

use app\assets\SmartAdminThemeAssets;
use app\models\sys\permissions\Permissions;
use app\models\sys\users\Users;
use app\models\sys\users\WebUser;
use app\modules\dol\models\DolAPI;
use app\modules\fraud\FraudModule;
use app\modules\history\HistoryModule;
use app\modules\notifications\NotificationsModule;
use app\modules\status\StatusModule;
use kartik\dialog\DialogBootstrapAsset;
use kartik\editable\EditableAsset;
use pozitronik\references\ReferencesModule;
use simialbi\yii2\rest\Connection;
use kartik\grid\Module as GridModule;
use odannyc\Yii2SSE\LibSSE;
use pozitronik\filestorage\FSModule;
use pozitronik\grid_config\GridConfigModule;
use pozitronik\sys_exceptions\SysExceptionsModule;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\caching\DummyCache;
use yii\log\FileTarget;
use yii\rest\UrlRule;
use yii\swiftmailer\Mailer;
use yii\web\JsonParser;

$params = require __DIR__.'/params.php';
$db = require __DIR__.'/db.php';
$statusRules = require __DIR__.'/status_rules.php';
$queue = require __DIR__.'/queue.php';

$config = [
	'id' => 'basic',
	'name' => 'DPL',
	'language' => 'ru-RU',
	'basePath' => dirname(__DIR__, 2),
	'bootstrap' => ['log', 'history', 'queue'],
	'homeUrl' => '/home/home',//<== строка, не массив
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
	],
	'modules' => [
		'gridview' => [
			'class' => GridModule::class
		],
		'sysexceptions' => [
			'class' => SysExceptionsModule::class,
			'defaultRoute' => 'index'
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
		'gridconfig' => [
			'class' => GridConfigModule::class
		],
		'references' => [
			'class' => ReferencesModule::class,
			'defaultRoute' => 'references',
			'params' => [
				'baseDir' => [
					'@app/models/',
				]
			]
		],
		'history' => [
			'class' => HistoryModule::class,
			'defaultRoute' => 'index'
		],
		'statuses' => [
			'class' => StatusModule::class,
			'params' => [
				'rules' => $statusRules
			]
		],
		'notifications' => [
			'class' => NotificationsModule::class
		],
		'fraud' => [
			'class' => FraudModule::class
		]
	],
	'components' => [
		'request' => [
			'cookieValidationKey' => 'cjhjrnsczxj3tpmzyd5jgeceyekb0fyfyf_',
			'parsers' => [
				'application/json' => JsonParser::class
			]
		],
		'cache' => [
//			'class' => FileCache::class,
			'class' => DummyCache::class//todo cache class autoselection
		],
		'user' => [
			'class' => WebUser::class,
			'identityClass' => Users::class,
			'enableAutoLogin' => true
		],
		'errorHandler' => [
			'errorAction' => 'site/error'
		],
		'mailer' => [
			'class' => Mailer::class,
			'useFileTransport' => true,
		],
		'log' => [
			'traceLevel' => 0,
			'targets' => [
				[
					'class' => FileTarget::class,
					'levels' => ['error', 'warning'],
				],
			],
		],
		'sse' => [
			'class' => LibSSE::class
		],
		'rest' => [
			'class' => Connection::class,
			'baseUrl' => 'http://bc/api',
//			 'auth' => function (Connection $db) {
//			      return 'admin: admin';
//			 },
			// 'auth' => 'Bearer: <mytoken>',
			// 'usePluralisation' => false,
			// 'useFilterKeyword' => false,
			// 'enableExceptions' => true,
			'itemsProperty' => 'items'
		],
		'db' => $db,
		'queue' => $queue,
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'enableStrictParsing' => false,
			'rules' => [
				['class' => UrlRule::class, 'controller' => 'api/users'],
//				'<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_m>/<_c>/<_a>',
//				'<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>' => '<_m>/<_c>/<_a>'
			]
		],
		'permissions' => [
			'class' => Permissions::class,
			/*
			 * Пути к расположениям контроллеров, для подсказок в выбиралках.
			 * Формат:
			 * 	алиас каталога => префикс id
			 * Так проще и быстрее, чем пытаться вычислять префикс из контроллера (в нём id появляется только в момент вызова,
			 * и зависит от множества настроек), учитывая, что это нужно только в админке, и только в выбиралке.
			 */
			'controllerDirs' => [
				'@app/controllers' => '',
				'@app/controllers/api' => 'api'
			],
			'grantAll' => [1]/*User ids, that receive all permissions by default*/
		],
		'dolApi' => [
			'class' => DolAPI::class,
			'baseUrl' => 'https://dolfront.beelinetst.ru/api/',
			'sslCertificate' => '@app/docker-data/cert/cacert.pem',//path for file, null for default, false for disable ssl
			'debugPhones' => [
				/* fake phone  =>  sms code */
				'9250000000' => '0000'
			]
		],
		'assetManager' => [
			'bundles' => [
				BootstrapPluginAsset::class => [
					'js' => []
				],
				BootstrapAsset::class => [
					'css' => [],
				],
				DialogBootstrapAsset::class => [
					'depends' => [
						SmartAdminThemeAssets::class
					]
				],
				EditableAsset::class => [
					'depends' => [
						SmartAdminThemeAssets::class
					]
				]
			]
		]
	],
	'params' => $params,
];

return $config;
