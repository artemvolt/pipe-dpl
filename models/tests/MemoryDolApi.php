<?php
declare(strict_types = 1);

namespace app\models\tests;

use app\modules\dol\components\v3\auth\confirmSms\ConfirmSmsResponse;
use app\modules\dol\components\v3\auth\smsLogOn\SmsLogonResponse;
use app\modules\dol\models\DolAPIInterface;

/**
 * Class MemoryDolApi
 * @package app\models\tests
 */
class MemoryDolApi implements DolAPIInterface {
	public $smses = [];
	public $smsLogon = [];
	public $confirmSmsLogon = [];

	/**
	 * @param string $phone
	 * @param string $message
	 * @return array
	 */
	public function sendSms(string $phone, string $message):array {
		$this->smses[] = compact('phone', 'message');
		return ['success' => true];
	}

	/**
	 * @param string $phoneAsLogin
	 * @return SmsLogonResponse
	 */
	public function smsLogon(string $phoneAsLogin):SmsLogonResponse {
		$this->smsLogon[] = $phoneAsLogin;
		return new SmsLogonResponse(['success' => true]);
	}

	/**
	 * @param string $phoneAsLogin
	 * @param string $code
	 * @return ConfirmSmsResponse
	 */
	public function confirmSmsLogon(string $phoneAsLogin, string $code):ConfirmSmsResponse {
		$this->confirmSmsLogon[] = [$phoneAsLogin, $code];
		return new ConfirmSmsResponse(['success' => true]);
	}
}