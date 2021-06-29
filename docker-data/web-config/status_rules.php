<?php
declare(strict_types = 1);

use app\models\reward\Rewards;
use app\models\seller\Sellers;
use app\models\sys\users\Users;

const SELLER_NOT_ACTIVE = 1;
const SELLER_ACTIVE = 2;
const SELLER_LIMITED = 3;
const SELLER_BLOCKED = 4;
const SELLER_SUSPENDED = 5;

return [
	Rewards::class => Rewards::class.'::status_config',//<== конфигурация возвращается в Rewards::status_config()
	Sellers::class => [
		SELLER_NOT_ACTIVE => [
			'id' => SELLER_NOT_ACTIVE,
			'name' => 'Не активирован',
			'initial' => true,
			'finishing' => false,
			'next' => [SELLER_ACTIVE, SELLER_LIMITED, SELLER_BLOCKED, SELLER_SUSPENDED],
			'allowed' => false
		],
		SELLER_ACTIVE => [
			'id' => SELLER_ACTIVE,
			'name' => 'Активирован',
			'initial' => false,
			'finishing' => true,
			'next' => [SELLER_NOT_ACTIVE, SELLER_LIMITED, SELLER_BLOCKED, SELLER_SUSPENDED],
			'allowed' => static function(Sellers $model, Users $user):bool {
				return true;
			},
			'style' => 'background: #ffa700; color:black'//стили можно задавать напрямую
		],
		SELLER_LIMITED => [
			'id' => SELLER_LIMITED,
			'name' => 'Ограничен',
			'initial' => false,
			'finishing' => true,
			'next' => [SELLER_NOT_ACTIVE, SELLER_ACTIVE, SELLER_BLOCKED, SELLER_SUSPENDED],
			'allowed' => static function(Sellers $model, Users $user):bool {
				return true;
			},
			'color' => '#00ff00'
		],
		SELLER_BLOCKED => [
			'id' => SELLER_BLOCKED,
			'name' => 'Заблокирован',
			'initial' => false,
			'finishing' => true,
			'next' => [SELLER_NOT_ACTIVE, SELLER_ACTIVE, SELLER_LIMITED, SELLER_SUSPENDED],
			'allowed' => static function(Sellers $model, Users $user):bool {
				return true;
			},
			'color' => '#00ff00'
		],
		SELLER_SUSPENDED => [
			'id' => SELLER_SUSPENDED,
			'name' => 'Suspend',
			'initial' => false,
			'finishing' => true,
			'next' => [SELLER_NOT_ACTIVE, SELLER_ACTIVE, SELLER_LIMITED, SELLER_BLOCKED],
			'allowed' => static function(Sellers $model, Users $user):bool {
				return true;
			},
			'color' => '#00ff00'
		]
	]
];
