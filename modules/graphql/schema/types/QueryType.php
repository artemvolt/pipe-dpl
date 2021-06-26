<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use app\models\sys\users\Users;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class QueryType
 * @package app\schema
 */
class QueryType extends ObjectType {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct([
			'fields' => [
				'examples' => [
					'type' => Type::listOf(Types::example()),
					'args' => [
						'search' => Type::string(),
					],
					'resolve' => function($root, $args) {
						return [
							new Users(['id' => $r1 = random_int(1, 10), 'username' => "hello$r1"]),
							new Users(['id' => $r2 = random_int(11, 20), 'username' => "hello$r2"]),
						];
					}
				],
				'example' => [
					'type' => Types::example(),
					'args' => [
						'id' => Type::nonNull(Type::int()),
					],
					'resolve' => function($root, $args) {
						return new Users(['id' => $args['id'], 'username' => 'hello5']);
					},
				],
			],
		]);
	}
}
