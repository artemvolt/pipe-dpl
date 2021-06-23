<?php
declare(strict_types = 1);

namespace app\schema;

use app\models\sys\users\active_record\Users;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class MutationType
 * @package app\schema
 */
class MutationType extends ObjectType {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$config = [
			'fields' => function() {
				return [
					'user' => [
						'type' => Types::usersMutation(),
						'args' => [
							'id' => Type::nonNull(Type::int()),
						],
						'resolve' => function($root, $args) {
							return Users::find()->where($args)->one();
						},
					],
				];
			}
		];

		parent::__construct($config);
	}
}