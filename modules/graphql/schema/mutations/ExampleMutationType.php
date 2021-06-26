<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\mutations;

use app\models\sys\users\Users;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use app\modules\graphql\schema\types\Types;

/**
 * Class ExampleMutationType
 * @package app\modules\graphql\schema\mutations
 */
class ExampleMutationType extends ObjectType implements MutationInterface {
	use MutationTrait;

	/**
	 * Список сообщений для popup на фронте
	 */
	public const MESSAGES = ['Ошибка сохранения партнера', 'Партнер успешно сохранен'];

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct([
			'fields' => [
				'update' => [
					'type' => Types::validationErrorsUnionType(Types::example()),
					'description' => 'Обновление',
					'args' => $this->getArgs(),
					'resolve' => function(array $argsFromMutationType, array $args) {
						$existentUser = new Users(['id' => $argsFromMutationType['id']]);
						$existentUser->load($args);
						return $this->getResult(false, [
							'username' => ['Пример ответа с ошибкой в поле']
						], self::MESSAGES);
					},
				],
				'create' => [
					'type' => Types::validationErrorsUnionType(Types::example()),
					'description' => 'Создание',
					'args' => $this->getArgs(),
					'resolve' => function(array $fromMutationArgs, array $args = []) {
						$user = new Users([
							'username' => $args['username']
						]);
						return $this->getResult(true, [], self::MESSAGES);
					},
				],
			]
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getArgs():array {
		return [
			'username' => [
				'type' => Type::nonNull(Type::string()),
				'description' => 'Наименование юридического лица партнера',
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMessages():array {
		return self::MESSAGES;
	}
}
