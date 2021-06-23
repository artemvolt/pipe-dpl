<?php
declare(strict_types = 1);

namespace app\schema;

use app\models\sys\users\active_record\Users;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class UsersType
 * Описание GraphQL-схемы для модели пользователя
 */
class UsersType extends ObjectType {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$config = [
			'fields' => function() {
				return [
					'username' => [
						'type' => Type::string(),
					],
					'login' => [
						'type' => Type::string(),
					],
					'email' => [
						'type' => Type::string(),
					],
					'create_date' => [
						'type' => Type::string(),

						/* текстовое описание, поясняющее
						 что именно хранит поле
						 немного позже вы увидите в чем его удобство
						 (оно еще больше сократит ваше общение с юайщиком)*/
						'description' => 'Date when user was created',

						// чтобы можно было форматировать дату, добавим
						// дополнительный аргумент format
						'args' => [
							'format' => Type::string(),
						],

						// и собственно опишем что с этим аргументом
						// делать
						'resolve' => function(Users $user, $args) {
							if (isset($args['format'])) {
								return date($args['format'], strtotime($user->create_date));
							}

							// коли ничего в format не пришло,
							// оставляем как есть
							return $user->create_date;
						},
					],

					'deleted' => [
						'type' => Type::int(),
					],

					// теперь самая интересная часть схемы -
					// связи
					'tokens' => [
						// так как адресов у нас много,
						// то нам необходимо применить
						// модификатор Type::listOf, который
						// указывает на то, что поле должно вернуть
						// массив объектов типа, указанного
						// в скобках
						'type' => Type::listOf(Types::tokens()),
						'resolve' => function(Users $user) {
							// примечательно то, что мы можем сразу же
							// обращаться к переменной $user без дополнительных проверок
							// вроде, не пустой ли он, и т.п.
							// так как если бы он был пустой, до текущего
							// уровня вложенности мы бы просто не дошли
							return $user->relatedUsersTokens;
						},
					],
				];
			}
		];

		parent::__construct($config);
	}
}
