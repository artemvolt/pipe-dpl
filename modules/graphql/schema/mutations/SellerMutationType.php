<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\mutations;

use app\models\seller\Sellers;
use app\models\sys\ValidateException;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use app\modules\graphql\schema\types\Types;
use Yii;

/**
 * Class ExampleMutationType
 * @package app\modules\graphql\schema\mutations
 */
class SellerMutationType extends ObjectType implements MutationInterface {
	use MutationTrait;

	/**
	 * Список сообщений для popup на фронте
	 */
	public const MESSAGES = ['Ошибка сохранения продавца', 'Продавец успешно сохранен'];

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct([
			'fields' => [
				'register' => [
					'type' => Types::validationErrorsUnionType(Types::seller()),
					'description' => 'Регистрация',
					'args' => $this->getArgs(),
					'resolve' => function(array $fromMutationArgs, array $args = []) {
						try {
							Yii::$app->db->transaction(function() use ($args) {
								$seller = new Sellers();
								return $seller->registerMini(
									$args['surname']??"",
									$args['name']??"",
									$args["patronymic"]??"",
									$args["phone_number"]??"",
									$args["email"]??""
								);
							});
						} catch (ValidateException $e) {
							return $this->getResult(false, $e->getErrors(), self::MESSAGES);
						}

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
			'surname' => [
				'type' => Type::nonNull(Type::string()),
				'description' => 'Фамилия',
			],
			'name' => [
				'type' => Type::nonNull(Type::string()),
				'description' => 'Имя',
			],
			'patronymic' => [
				'type' => Type::string(),
				'description' => 'Отчество',
			],
			'phone_number' => [
				'type' => Type::nonNull(Type::string()),
				'description' => 'Номер телефона',
			],
			'email' => [
				'type' => Type::nonNull(Type::string()),
				'description' => 'Электронная почта',
			],
			'accept_agreement' => [
				'type' => Type::boolean(),
				'description' => 'Согласие с условиями соглашения'
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
