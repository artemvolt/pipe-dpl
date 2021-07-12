<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class SellerInviteLinkType
 * @package app\modules\graphql\schema\types
 */
class SellerInviteLinkType extends ObjectType {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct([
			'fields' => [
				'phone_number' => [
					'type' => Type::string(),
					'description' => 'Номер телефона',
				],
				'email' => [
					'type' => Type::string(),
					'description' => 'Email',
				]
			],
		]);
	}
}
