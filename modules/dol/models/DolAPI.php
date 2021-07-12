<?php
declare(strict_types = 1);

namespace app\modules\dol\models;

use app\models\phones\Phones;
use app\modules\dol\components\confirmSmsLogon\ConfirmSmsLogonHandler;
use app\modules\dol\components\confirmSmsLogon\SmsLogonHandler;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use DateTime;
use RuntimeException;
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
 */
class DolAPI extends ActiveRecord {
	public string $baseUrl = "https://dolfront.beelinetst.ru/api/";

	public const METHOD_SMS_LOGON = 'v2/auth/sms-logon';
	public const METHOD_CONFIRM_SMS_LOGON = 'v2/auth/confirm-sms-logon';
	public const METHOD_REFRESH = 'v2/auth/refresh';

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
	 * @return Response
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 */
	protected function doRequest(string $url, array $data):Response {
		$client = new Client([
			'transport' => CurlTransport::class
		]);
		$request = $client->createRequest();
		$request->method = 'POST';
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
		$request->format = Client::FORMAT_JSON;
		$request->fullUrl = $url;
		$request->data = $data;//json_encode($data);
		return $request->send();
	}

	/**
	 * @param string $phoneAsLogin
	 * @return string
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 * @throws ValidateServerErrors
	 */
	public function smsLogon(string $phoneAsLogin):string {
		$phoneFormat = Phones::nationalFormat($phoneAsLogin);
		if (ArrayHelper::keyExists($phoneAsLogin, $this->_debugPhones)) {
			return (new DateTime())->format("Y-m-d H:i:s");
		}

		$response = $this->doRequest($this->baseUrl.self::METHOD_SMS_LOGON, [
			'phoneAsLogin' => $phoneFormat
		]);
		$handler = new SmsLogonHandler();
		$content = $handler->handle($response);
		return $content['smsCodeExpiration'];
	}

	/**
	 * @param string $phoneAsLogin
	 * @param string $code
	 * @return bool
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 * @throws ValidateServerErrors
	 */
	public function confirmSmsLogon(string $phoneAsLogin, string $code):bool {
		$phoneFormat = Phones::nationalFormat($phoneAsLogin);
		if ($code === ArrayHelper::getValue($this->_debugPhones, $phoneAsLogin)) {
			return true;
		}
		$response = $this->doRequest($this->baseUrl.self::METHOD_CONFIRM_SMS_LOGON, compact('phoneFormat', 'code'));
		$handler = new ConfirmSmsLogonHandler();
		$handler->handle($response);
		return true;
	}

	/**
	 * @return bool[]
	 */
	public function sendSms(string $phone, string $message):array {
		if (YII_DEBUG || YII_ENV_TEST) return ["success" => true, 'phone' => $phone, 'message' => $message];
		throw new RuntimeException("Realize this method");
	}
}
