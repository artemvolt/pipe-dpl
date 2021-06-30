<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use app\models\seller\active_record\SellersAR;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class ExampleType
 * @package app\modules\graphql\schema\types
 */
class ProfileType extends ObjectType {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$seller = new SellersAR();

		parent::__construct([
			'fields' => [
				'last_name' => [
					'type' => Type::string(),
					'description' => $seller->getAttributeLabel('surname'),
				],
				'first_name' => [
					'type' => Type::string(),
					'description' => $seller->getAttributeLabel('name'),
				],
				'middle_name' => [
					'type' => Type::string(),
					'description' => $seller->getAttributeLabel('patronymic'),
				],
				'phone_number' => [
					'type' => Type::string(),
					'description' => 'Номер телефона'
				],
				'login' => [
					'type' => Type::string(),
					'description' => 'Логин',
				],
				'email' => [
					'type' => Type::string(),
					'description' => 'Электронная почта',
				],
			],
		]);
	}
}
