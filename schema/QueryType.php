<?php
declare(strict_types = 1);

namespace app\schema;

use app\models\sys\users\active_record\Users;
use app\models\sys\users\active_record\UsersTokens;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class QueryType
 * @package app\schema
 */
class QueryType extends ObjectType {
	public function __construct() {
		$config = [
			'fields' => function() {
				return [
					'user' => [
						'type' => Types::users(),

						// добавим сюда аргументов, дабы
						// выбрать необходимого нам юзера
						'args' => [
							// чтобы аргумент сделать обязательным
							// применим модификатор Type::nonNull()
							'id' => Type::nonNull(Type::int()),
						],
						'resolve' => function($root, $args) {
							// таким образом тут мы уверены в том
							// что в $args обязательно присутствует элемент с индексом
							// `id`, и он обязательно целочисленный, иначе мы бы сюда не попали

							// так же мы не боимся, что юзера с этим `id`
							// в базе у нас не существует
							// библиотека корректно это обработает
							return Users::find()->where(['id' => $args['id']])->one();
						}
					],

					// в принципе на поле user можно остановиться, в случае
					// если нам нужно обращаться к данным лиш конкретного пользователя
					// но если нам нужны данные с другими привязками добавим
					// для примера еще полей

					'tokens' => [
						// без дополнительных параметров
						// просто вернет нам список всех
						// адресов
						'type' => Type::listOf(Types::tokens()),

						// добавим фильтров для интереса
						'args' => [
							'auth_token' => Type::string(),
							'user_id' => Type::int(),
						],
						'resolve' => function($root, $args) {
							$query = UsersTokens::find();

							if (!empty($args)) {
								$query->where($args);
							}

							return $query->all();
						}
					],
				];
			}
		];

		parent::__construct($config);
	}
}