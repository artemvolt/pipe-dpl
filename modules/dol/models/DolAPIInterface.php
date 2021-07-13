<?php
declare(strict_types = 1);

namespace app\modules\dol\models;

use app\modules\dol\components\exceptions\ValidateServerErrors;
use app\modules\dol\components\v3\auth\confirmSms\ConfirmSmsResponse;
use app\modules\dol\components\v3\auth\smsLogOn\SmsLogonResponse;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception as HttpClientException;

/**
 * Class DolAPI
 * @property-read DolAuthToken $authToken Объект токена, используемый для подписи запросов
 */
interface DolAPIInterface {
	/**
	 * @param string $phoneAsLogin
	 * @return SmsLogonResponse
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 * @throws ValidateServerErrors
	 */
	public function smsLogon(string $phoneAsLogin):SmsLogonResponse;

	/**
	 * @param string $phoneAsLogin
	 * @param string $code
	 * @return ConfirmSmsResponse
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 * @throws ValidateServerErrors
	 */
	public function confirmSmsLogon(string $phoneAsLogin, string $code):ConfirmSmsResponse;

	/**
	 * @return bool[]
	 */
	public function sendSms(string $phone, string $message):array;
}