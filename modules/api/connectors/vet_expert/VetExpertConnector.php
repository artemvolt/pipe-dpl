<?php
declare(strict_types = 1);

namespace app\modules\api\connectors\vet_expert;

use app\modules\api\connectors\BaseHttpConnector;
use pozitronik\helpers\ArrayHelper;
use RuntimeException;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception as HttpClientException;

/**
 * Class VetExpertConnector
 * @package app\modules\api\connectors
 */
class VetExpertConnector extends BaseHttpConnector {
	private ?string $_authToken;

	/**
	 * VetExpertConnector constructor.
	 * @param array $config
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public function __construct(array $config = []) {
		/** @var array $params */
		$params = ArrayHelper::getValue(Yii::$app->params, 'vet-expert.connector', new InvalidConfigException("Не заданы параметры коннектора"));

		if (null === $this->_authToken = ArrayHelper::remove($params, 'authToken')) {
			throw new InvalidConfigException("Токен не задан");
		}

		parent::__construct($params, $config);
	}

	/**
	 * Заведение/обновление подписки абонента.
	 * @param SubscriptionParams $params
	 * @throws HttpClientException
	 * @throws Throwable
	 */
	public function makeSubscribe(SubscriptionParams $params):void {
		$response = $this->getClient()
			->post('/callback', $params->toArray(), ['Authorization' => 'Bearer '.$this->getToken()])
			->send();

		if (!$response->isOk || (true !== ArrayHelper::getValue($response->data, 'success'))) {
			throw new RuntimeException('Запрос на подписку для VetExpert выполнился с ошибкой: '.$response->toString());
		}
	}

	/**
	 * Запрос Bearer-токена. Время жизни токена - 15 минут.
	 * @return string
	 * @throws RuntimeException
	 */
	private function getToken():string {
		return Yii::$app->cache->getOrSet(
			'api:vet-expert:token',
			function() {
				$response = $this->getClient()
					->get('/get_token', null, ['Authorization' => 'Basic '.$this->_authToken])
					->send();

				$token = $response->isOk?ArrayHelper::getValue($response->data, 'token'):null;
				if (null === $token) {
					throw new RuntimeException('Запрос на получение токена от VetExpert выполнился с ошибкой: '.$response->toString());
				}

				return $token;
			}, 900);
	}
}