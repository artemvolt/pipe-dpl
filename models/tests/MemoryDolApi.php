<?php
declare(strict_types = 1);

namespace app\models\tests;

/**
 * Class MemoryDolApi
 * @package app\models\tests
 */
class MemoryDolApi {
	public $smses = [];
	public $smsLogon = [];
	public $confirmSmsLogon = [];

	/**
	 * @param string $phone
	 * @param string $text
	 * @return array
	 */
	public function sendSms(string $phone, string $text):array {
		$this->smses[] = compact('phone', 'text');
		return ['success' => true];
	}

	/**
	 * @param string $phone
	 * @return bool[]
	 */
	public function smsLogon(string $phone):array {
		$this->smsLogon[] = $phone;
		return ['success' => true];
	}

	/**
	 * @param string $phone
	 * @param string $sms
	 * @return bool[]
	 */
	public function confirmSmsLogon(string $phone, string $sms):array {
		$this->confirmSmsLogon[] = [$phone, $sms];
		return ['success' => true];
	}
}