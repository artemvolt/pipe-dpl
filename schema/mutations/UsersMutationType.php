<?php
declare(strict_types = 1);

namespace app\schema\mutations;

use app\models\sys\users\active_record\Users;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class UsersMutationType
 * @package app\schema\mutations
 */
class UsersMutationType extends ObjectType {
	public function __construct() {
		$config = [
			'fields' => function() {
				return [
					// для теста реализуем здесь
					// один метод для изменения данных
					// объекта User
					'update' => [
						// какой должен быть возвращаемый тип
						// здесь 2 варианта - либо
						// булев - удача / неудача
						// либо же сам объект типа User.
						// позже мы поговорим о валидации
						// тогда всё станет яснее, а пока
						// оставим булев для простоты
						'type' => Type::boolean(),
						'description' => 'Update user data.',
						'args' => [
							// сюда засунем все то, что
							// разрешаем изменять у User.
							// в примере оставим все поля необязательными
							// но просто если нужно, то можно
							'username' => Type::string(),
							'email' => Type::string(),
						],
						'resolve' => function(Users $user, $args) {
							// ну а здесь всё проще простого,
							// т.к. библиотека уже все проверила за нас:
							// есть ли у нас юзер, правильные ли у нас
							// аргументы и всё ли пришло, что необходимо
							$user->setAttributes($args);
							return $user->save();
						}
					],
				];
			}
		];

		parent::__construct($config);
	}
}