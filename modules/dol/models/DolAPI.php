<?php
declare(strict_types = 1);

namespace app\modules\dol\models;

use app\modules\dol\components\confirmSmsLogon\ConfirmSmsLogonHandler;
use app\modules\dol\components\confirmSmsLogon\SmsLogonHandler;
use app\modules\dol\components\requestUserProfile\RequestUserProfileHandler;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use RuntimeException;
use DateTime;
use app\models\phones\Phones;
use simialbi\yii2\rest\ActiveRecord;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\UnauthorizedHttpException;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception as HttpClientException;
use yii\httpclient\Response;

/**
 * Class DolAPI
 * @property-read DolAuthToken $authToken Объект токена, используемый для подписи запросов
 */
class DolAPI extends ActiveRecord {
	public ?string $baseUrl = null;

	public const METHOD_SMS_LOGON = 'v3/auth/sms-logon';
	public const METHOD_CONFIRM_SMS_LOGON = 'v3/auth/confirm-sms';
	public const METHOD_REFRESH = 'v3/auth/refresh';
	public const METHOD_USER = 'v3/auth/user';

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
	protected function doRequest(string $url, array $data = [], string $method = 'POST'):Response {
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
	 * @param string $phoneAsLogin
	 * @return array
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 * @throws ValidateServerErrors
	 */
	public function smsLogon(string $phoneAsLogin):array {
		$phoneFormat = Phones::nationalFormat($phoneAsLogin);
		if (ArrayHelper::keyExists($phoneAsLogin, $this->_debugPhones)) {
			return [
				'success' => true,
				'smsCodeExpiration' => (new DateTime())->format("Y-m-d H:i:s")
			];
		}

		$response = $this->doRequest($this->baseUrl.self::METHOD_SMS_LOGON, [
			'phoneAsLogin' => $phoneFormat
		]);
		$handler = new SmsLogonHandler();
		return $handler->handle($response);
	}

	/**
	 * @param string $phoneAsLogin
	 * @param string $code
	 * @return array
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 * @throws ValidateServerErrors
	 */
	public function confirmSmsLogon(string $phoneAsLogin, string $code):array {
		$phoneFormat = Phones::nationalFormat($phoneAsLogin);
		if ($code === ArrayHelper::getValue($this->_debugPhones, $phoneAsLogin)) {
			return ['success' => true];
		}
		$response = $this->doRequest($this->baseUrl.self::METHOD_CONFIRM_SMS_LOGON, compact('phoneFormat', 'code'));
		$handler = new ConfirmSmsLogonHandler();
		return $handler->handle($response);
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
	 * @throws ValidateServerErrors
	 * @throws ForbiddenHttpException
	 * @throws UnauthorizedHttpException
	 */
	public function requestUserProfile():array {
		$response = $this->doRequest($this->baseUrl.self::METHOD_USER, [], 'GET');
		$handler = new RequestUserProfileHandler();
		return $handler->handle($response);
	}

	/**
	 * @return bool[]
	 */
	public function sendSms(string $phone, string $message):array {
		if (YII_DEBUG || YII_ENV_TEST) return ["success" => true, 'phone' => $phone, 'message' => $message];
		throw new RuntimeException("Realize this method");
	}
}
