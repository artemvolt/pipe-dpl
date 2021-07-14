<?php
declare(strict_types = 1);

namespace app\modules\dol\models;

use app\modules\dol\components\v3\auth\confirmSms\ConfirmSmsHandler;
use app\modules\dol\components\v3\auth\confirmSms\ConfirmSmsResponse;
use app\modules\dol\components\v3\auth\register\CheckCodeHandler;
use app\modules\dol\components\v3\auth\register\CheckCodeResponse;
use app\modules\dol\components\v3\auth\register\RegisterHandler;
use app\modules\dol\components\v3\auth\register\RegisterResponse;
use app\modules\dol\components\v3\auth\smsLogOn\SmsLogonHandler;
use app\modules\dol\components\requestUserProfile\RequestUserProfileHandler;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use app\modules\dol\components\v3\auth\smsLogOn\SmsLogonResponse;
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
class DolAPI extends ActiveRecord implements DolAPIInterface {
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
	 * @param DolAuthToken $token
	 */
	public function changeAuthToken(DolAuthToken $token):void {
		$this->_authToken = $token;
	}

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
	 * @return SmsLogonResponse
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 * @throws ValidateServerErrors
	 */
	public function smsLogon(string $phoneAsLogin):SmsLogonResponse {
		$phoneFormat = Phones::nationalFormat($phoneAsLogin);
		if (ArrayHelper::keyExists($phoneAsLogin, $this->_debugPhones)) {
			return new SmsLogonResponse([
				'success' => true,
				'smsCodeExpiration' => (new DateTime())->format("Y-m-d H:i:s")
			]);
		}

		$response = $this->doRequest($this->baseUrl.self::METHOD_SMS_LOGON, [
			'phoneAsLogin' => $phoneFormat
		]);
		$handler = new SmsLogonHandler();
		$handler->handle($response);
		return SmsLogonResponse::fromJsonString($response->content);
	}

	/**
	 * @param string $phoneAsLogin
	 * @param string $code
	 * @return ConfirmSmsResponse
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 * @throws ValidateServerErrors
	 */
	public function confirmSmsLogon(string $phoneAsLogin, string $code):ConfirmSmsResponse {
		$phoneFormat = Phones::nationalFormat($phoneAsLogin);
		if ($code === ArrayHelper::getValue($this->_debugPhones, $phoneAsLogin)) {
			return new ConfirmSmsResponse(['success' => true]);
		}
		$response = $this->doRequest($this->baseUrl.self::METHOD_CONFIRM_SMS_LOGON, compact('phoneFormat', 'code'));
		$handler = new ConfirmSmsHandler();
		$handler->handle($response);
		return ConfirmSmsResponse::fromJsonString($response->content);
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

	/**
	 * @param string $phoneAsLogin
	 * @return RegisterResponse
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 * @throws ValidateServerErrors
	 */
	public function register(string $phoneAsLogin):RegisterResponse {
		$phoneFormat = Phones::nationalFormat($phoneAsLogin);
		$response = $this->doRequest("/api/v3/auth/register", ['phoneAsLogin' => $phoneFormat]);
		(new RegisterHandler())->handle($response);
		return RegisterResponse::fromJsonString($response->content);
	}

	/**
	 * @param string $phoneAsLogin
	 * @param string $code
	 * @param string $verificationToken
	 * @return CheckCodeResponse
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 * @throws ValidateServerErrors
	 */
	public function checkCode(string $phoneAsLogin, string $code, string $verificationToken):CheckCodeResponse {
		$phoneFormat = Phones::nationalFormat($phoneAsLogin);
		$response = $this->doRequest('/api/v3/auth/register/check-code', [
			'phoneAsLogin' => $phoneFormat,
			'code' => $code,
			'verificationToken' => $verificationToken
		]);
		(new CheckCodeHandler())->handle($response);
		return CheckCodeResponse::fromJsonString($response->content);
	}
}
