<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class MutationType
 * @package app\modules\graphql\schema\types
 */
class MutationType extends ObjectType {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct([
			'fields' => [
				'example' => [
					'type' => Types::exampleMutation(),
					'args' => [
						/**
						 * Передаем id для удобства семантики, не
						 * mutation {
						 *    example {
						 *        update(id:....){..}
						 *  }
						 * }
						 * а
						 * mutation {
						 *     example(id:5) {
						 *        update(...){...}
						 *   }
						 * }
						 *
						 */
						'id' => Type::int(),
					],
					'resolve' => function($root, $args) {
						return $args;
					},
				],
			]
		]);
	}
}
