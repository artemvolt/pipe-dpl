<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\mutations;

use app\components\exceptions\ValidateException;
use app\models\seller\RegisterMiniSellerForm;
use app\models\seller\SellerMiniConfirmSmsForm;
use app\models\seller\SellerMiniService;
use app\modules\dol\components\exceptions\ServerDomainError;
use app\modules\dol\components\exceptions\ValidateServerErrors;
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
				'confirmSms' => [
					'type' => Types::response(),
					'args' => [
						'phone_number' => Type::nonNull(Type::string()),
						'sms' => Type::nonNull(Type::string())
					],
					'resolve' => function(array $fromMutation, array $args = []) {
						$service = new SellerMiniService();
						try {
							$isConfirm = $service->confirmSms(new SellerMiniConfirmSmsForm([
								'phone_number' => $args['phone_number'],
								'sms' => $args['sms']
							]));
							return ['result' => $isConfirm];
						} catch (ValidateException $e) {
							return $this->getResult(false, $e->getErrors(), ['Ошибка запроса']);
						} catch (ValidateServerErrors | ServerDomainError $e) {
							/**
							 * @TODO обсудить с фронтом пару моментов
							 * с форматом ответа
							 */
							return $this->getResult(false, [], ['Ошибка сервиса']);
						}
					}
				]
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
