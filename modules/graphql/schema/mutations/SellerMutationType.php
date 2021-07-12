<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\mutations;

use app\components\exceptions\ValidateException;
use app\models\seller\RegisterMiniSellerForm;
use app\models\seller\SellerMiniService;
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
								$service = new SellerMiniService();
								$service->register(new RegisterMiniSellerForm($args));
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
			'phone_number' => [
				'type' => Type::nonNull(Type::string()),
				'description' => 'Номер телефона',
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
