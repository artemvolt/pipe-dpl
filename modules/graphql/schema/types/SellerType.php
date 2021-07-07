<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use app\models\seller\active_record\SellersAR;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class SellerType
 * @package app\modules\graphql\schema\types
 */
class SellerType extends ObjectType {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$seller = new SellersAR();

		parent::__construct([
			'fields' => [
				'surname' => [
					'type' => Type::string(),
					'description' => $seller->getAttributeLabel('surname'),
				],
				'name' => [
					'type' => Type::string(),
					'description' => $seller->getAttributeLabel('name'),
				],
				'patronymic' => [
					'type' => Type::string(),
					'description' => $seller->getAttributeLabel('patronymic'),
				],
				'phone_number' => [
					'type' => Type::string(),
					'description' => 'Номер телефона'
				],
				'email' => [
					'type' => Type::string(),
					'description' => 'Электронная почта',
				],
			],
		]);
	}
}
