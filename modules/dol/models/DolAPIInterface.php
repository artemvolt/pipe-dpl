<?php
declare(strict_types = 1);

namespace app\modules\dol\models;

use app\modules\dol\components\v3\auth\confirmSms\ConfirmSmsResponse;
use app\modules\dol\components\v3\auth\register\RegisterResponse;
use app\modules\dol\components\v3\auth\smsLogOn\SmsLogonResponse;

/**
 * Class DolAPI
 * @property-read DolAuthToken $authToken Объект токена, используемый для подписи запросов
 */
interface DolAPIInterface {
	/**
	 * @param string $phoneAsLogin
	 * @return SmsLogonResponse
	 */
	public function smsLogon(string $phoneAsLogin):SmsLogonResponse;

	/**
	 * @param string $phoneAsLogin
	 * @param string $code
	 * @return ConfirmSmsResponse
	 */
	public function confirmSmsLogon(string $phoneAsLogin, string $code):ConfirmSmsResponse;

	/**
	 * @return bool[]
	 */
	public function sendSms(string $phone, string $message):array;

	/**
	 * @param string $phoneAsLogin
	 * @return RegisterResponse
	 */
	public function register(string $phoneAsLogin):RegisterResponse;
}