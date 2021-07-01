<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use GraphQL\Type\Definition\ObjectType;

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
				'seller' => [
					'type' => Types::sellerMutation(),
					'resolve' => function($root, $args) {
						return $args;
					}
				]
			]
		]);
	}
}
