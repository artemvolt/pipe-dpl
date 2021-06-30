<?php
declare(strict_types = 1);

use app\models\products\SimCard;
use app\models\seller\Sellers;
use app\models\sys\permissions\active_record\PermissionsCollections;
use app\models\sys\permissions\Permissions;
use app\models\sys\users\Users;

return [
	'bsVersion' => '4',
	'searchConfig' => [
		'Users' => [//<== алиас модели
			'class' => Users::class,//<== FQN-название ActiveRecord-класса
			'templateView' => '@app/views/users/search-template', //<== путь до шаблона отображения результата поиска
			/*
			 * можно вписать строку шаблона напрямую, этот параметр приоритетнее
			 * 'template' => '<div class="suggestion-item"><div class="suggestion-name">{{name}}</div><div class="clearfix"></div><div class="suggestion-secondary">{{controller}}</div><div class="suggestion-links"><a href="'.PermissionsController::to('edit').'?id={{id}}" class="dashboard-button btn btn-xs btn-info pull-left">Редактировать<a/></div><div class="clearfix"></div></div>',
			 */
			'header' => 'Пользователи', //<== заголовок в поисковом выводе
			//'limit' => 5,// <== лимит поиска,
			//'url' => AjaxController::to('search') // <== Url входящего поискового экшена
			'attributes' => [// <== поисковые атрибуты, см. SearchHelper::Search $searchAttributes
				'username',
				'comment',
				'email'
			]
		],
		'Permissions' => [
			'class' => Permissions::class,
			'templateView' => '@app/views/permissions/search-template',
			'header' => 'Доступы',
			'attributes' => [
				'name',
				'controller'
			]
		],
		'PermissionsCollections' => [
			'class' => PermissionsCollections::class,
			'templateView' => '@app/views/permissions-collections/search-template',
			'header' => 'Группы доступов',
			'attributes' => [
				'name',
			]
		],
		'Sellers' => [
			'class' => Sellers::class,
			'templateView' => '@app/views/sellers/search-template',
			'header' => 'Продавцы',
			'attributes' => [
				'name',
			]
		]
	],
	'productsConfig' => [
		1 => [
			'class' => SimCard::class,
			'name' => 'Сим-карты'
		]
	]

];
