<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class SellerMiniConfirmSmsType
 * @package app\modules\graphql\schema\types
 */
class SellerMiniConfirmSmsType extends ObjectType {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct([
			'fields' => [
				'result' => [
					'type' => Type::boolean(),
					'description' => 'Результат подтверждения',
				]
			],
		]);
	}
}
