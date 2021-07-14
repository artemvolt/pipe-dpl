<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\mutations;

use app\components\exceptions\ValidateException;
use app\models\seller\SellerMiniConfirmSmsForm;
use app\models\seller\SellerMiniService;
use app\modules\dol\components\exceptions\ServerDomainError;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use app\modules\graphql\schema\types\Types;

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
						} catch (ValidateServerErrors $e) {
							return $this->getResult(false, $e->mapErrors([
								'phoneAsLogin' => 'phone_number',
								'Code' => 'sms'
							]), ['Ошибка при выполнении']);
						} catch (ServerDomainError $e) {
							return $this->getResult(false, [
								'phone_number' => $e->getMessage()
							], ['Ошибка при выполнении']);
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
