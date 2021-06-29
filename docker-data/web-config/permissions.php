<?php
declare(strict_types = 1);

use app\models\sys\permissions\Permissions;

return [
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
	'grantAll' => [1],/*User ids, that receive all permissions by default*/
	'grant' => [/*перечисление прямых назначений*/
		1 => ['login_as_another_user', 'some_other_permission']
	],
	'permissions' => [//параметры контроллер-экшен-etc в этой конфигурации не поддерживаются
		'system' => [
			'comment' => 'Разрешение на доступ к системным параметрам',
		],
		'login_as_another_user' => [
			'comment' => 'Разрешение авторизоваться под другим пользователем',
		],
		'dealer_sellers' => [
			'comment' => 'Фильтрация продавцов по дилеру. Показываем всех продавцов дилера, но не больше.'
		],
		'dealer_store_sellers' => [
			'comment' => 'Фильтрация продавцов по магазину. Показываем всех продавцов магазина, но не больше.'
		],
		'dealer_stores' => [
			'comment' => 'Фильтрация магазинов по дилеру. Показываем всех магазинов дилера, но не больше.'
		],
		'manager_store' => [
			'comment' => 'Фильтрация магазинов по менеджеру. Показываем всех магазинов менеджера, но не больше.'
		],
		'manager_dealer' => [
			'comment' => 'Фильтрация дилеров по менеджеру. Показываем всех дилеров менеджера, но не больше.'
		],
		'dealer_managers' => [
			'comment' => 'Фильтрация менеджеров по дилеру. Показываем всех менеджеров дилера, но не больше.'
		],
		'show_all_dealers' => [
			'comment' => 'Показываем все магазины.'
		],
		'show_all_stores' => [
			'comment' => 'Показываем все дилеры.'
		],
		'show_all_managers' => [
			'comment' => 'Показываем все менеджеры.'
		],
		'show_all_sellers' => [
			'comment' => 'Показываем все товары.'
		]
	]
];