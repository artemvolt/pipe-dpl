<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Тип для серверной валидации моделей
 * Class ValidationErrorType
 * @package app\modules\graphql\schema\types
 */
class ValidationErrorType extends ObjectType {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct([
			'fields' => [
				'field' => [
					'type' => Type::string(),
					'description' => 'Наименование атрибута, при валидации которого произошла ошибка',
				],
				'messages' => [
					'type' => Type::listOf(Type::string()),
					'description' => 'Массив ошибок атрибута',
				],
			],
		]);
	}
}
