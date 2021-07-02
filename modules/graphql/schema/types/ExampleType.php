<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class ExampleType
 * @package app\modules\graphql\schema\types
 */
class ExampleType extends ObjectType {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct([
			'fields' => [
				'id' => [
					'type' => Type::int(),
					'description' => 'Идентификатор пользователя',
				],
				'username' => [
					'type' => Type::string(),
					'description' => 'Ник пользователя',
				]
			],
		]);
	}
}
