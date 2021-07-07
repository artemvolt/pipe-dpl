<?php
declare(strict_types = 1);

namespace app\modules\dol\models;

use Exception;
use simialbi\yii2\rest\ActiveRecord;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception as HttpClientException;
use yii\httpclient\Response;

/**
 * Class DolAPI
 * @property-read null|bool $success Last operation response status
 * @property-read string $errorMessage Last operation error message
 * @property-read DolAuthToken $authToken Объект токена, используемый для подписи запросов
 */
class DolAPI extends ActiveRecord {
	public string $baseUrl = "https://dolfront.beelinetst.ru/api/";

	public const METHOD_SMS_LOGON = 'v2/auth/sms-logon';
	public const METHOD_CONFIRM_SMS_LOGON = 'v2/auth/confirm-sms-logon';
	public const METHOD_REFRESH = 'v2/auth/refresh';
	public const METHOD_USER = 'v2/auth/user';

	/**
	 * @var bool|null
	 */
	public ?bool $success = null;

	/**
	 * @var string
	 */
	public string $errorMessage = '';

	/**
	 * @var array
	 */
	private array $_debugPhones = [];

	/**
	 * @var null|string|false
	 */
	private $_sslCertificate; //null - default, string - file, false - disabled

	/**
	 * @var DolAuthToken|null
	 */
	private ?DolAuthToken $_authToken = null;

	/**
	 * @inheritDoc
	 */
	public function init():void {
		$this->baseUrl = ArrayHelper::getValue(Yii::$app->components, "dolApi.baseUrl", $this->baseUrl);
		$this->_debugPhones = ArrayHelper::getValue(Yii::$app->components, "dolApi.debugPhones", $this->_debugPhones);
		$this->_sslCertificate = ArrayHelper::getValue(Yii::$app->components, "dolApi.sslCertificate", $this->_sslCertificate);
	}

	/**
	 * @param string $url
	 * @param array $data
	 * @param string $method
	 * @return Response
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 */
	private function doRequest(string $url, array $data = [], string $method = 'POST'):Response {
		$client = new Client([
			'transport' => CurlTransport::class
		]);
		$request = $client->createRequest();
		$request->method = $method;

		if (false === $this->_sslCertificate) {
			$request->addOptions([
				'sslVerifyPeer' => false
			]);
		} elseif (is_string($this->_sslCertificate)) {
			$request->addOptions([
				'sslCafile' => $this->_sslCertificate
			]);
		}

		$request->headers = [
			'accept' => 'text/plain',
			'Content-Type' => 'application/json'
		];
		if ($this->authToken->value) {
			$request->headers['Authorization'] = 'Bearer '.$this->authToken->value;
		}
		$request->format = Client::FORMAT_JSON;
		$request->fullUrl = $url;
		if ($data) {
			$request->data = $data;
		}
		return $request->send();
	}

	/**
	 * @param string $answer
	 * @return array
	 * @throws Exception
	 */
	private function parseAnswer(string $answer):array {
		$this->success = false;
		if (null === $result = json_decode($answer, true, 512, JSON_OBJECT_AS_ARRAY)) {
			$this->errorMessage = 'Ошибка парсинга ответа DOL API';
			return [];
		}
		if ($this->success = ArrayHelper::getValue($result, 'success', $this->success)) {
			return $result;
		}
		$this->errorMessage = 'Ошибка запроса DOL API';
		return $result;
	}

	/**
	 * @param string $phoneAsLogin
	 * @return array
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 */
	public function smsLogon(string $phoneAsLogin):array {
		if (ArrayHelper::keyExists($phoneAsLogin, $this->_debugPhones)) {
			$this->success = true;
			return [
				"success" => true
			];
		}
		$response = $this->doRequest($this->baseUrl.self::METHOD_SMS_LOGON, [
			'phoneAsLogin' => $phoneAsLogin
		]);
		return $this->parseAnswer($response->content);
	}

	/**
	 * @param string $phoneAsLogin
	 * @param string $code
	 * @return array
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 */
	public function confirmSmsLogon(string $phoneAsLogin, string $code):array {
		if ($code === ArrayHelper::getValue($this->_debugPhones, $phoneAsLogin)) {
			$this->success = true;
			return [
				"success" => true
			];
		}
		$response = $this->doRequest($this->baseUrl.self::METHOD_CONFIRM_SMS_LOGON, compact('phoneAsLogin', 'code'));
		return $this->parseAnswer($response->content);
	}

	/**
	 * @return DolAuthToken
	 */
	public function getAuthToken():DolAuthToken {
		if (null === $this->_authToken) $this->_authToken = new DolAuthToken();
		return $this->_authToken;
	}

	/**
	 * @return array
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 */
	public function getUserProfile():array {
		$response = $this->doRequest($this->baseUrl.self::METHOD_USER, [], 'GET');
		return $this->parseGetAnswer($response->content);
	}

	/**
	 * @param string $answer
	 * @return array
	 * @throws Exception
	 */
	private function parseGetAnswer(string $answer):array {
		if (null === $result = json_decode($answer, true, 512, JSON_OBJECT_AS_ARRAY)) {
			$this->errorMessage = 'Ошибка парсинга ответа DOL API';
		}
		return $result;
	}
}