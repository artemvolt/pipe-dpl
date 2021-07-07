<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use app\models\seller\Sellers;
use GraphQL\Type\Definition\ObjectType;

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
				'profile' => [
					'type' => Types::profile(),
					/**
					 * @TODO сделать реализацию метода. Возвращать на основе Yii::$app->user->identity
					 */
					'resolve' => function() {
						return new Sellers();
					}
				]
			],
		]);
	}
}
