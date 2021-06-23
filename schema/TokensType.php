<?php
declare(strict_types = 1);

namespace app\schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class TokensType
 * @package app\schema
 */
class TokensType extends ObjectType {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$config = [
			'fields' => function() {
				return [
					'user_id' => [
						'type' => Type::int(),
					],
					'auth_token' => [
						'type' => Type::string(),
					],
					'create_date' => [
						'type' => Type::string(),

					],

					// при необходимости с остальными датами можно
					// произвести те же действия, но мы
					// сейчас этого делать, конечно же, не будем
					'created' => [
						'type' => Type::string(),
					],
				];
			}
		];

		parent::__construct($config);
	}
}