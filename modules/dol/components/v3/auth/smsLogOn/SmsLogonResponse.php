<?php
declare(strict_types = 1);

namespace app\modules\dol\components\v3\auth\smsLogOn;

use app\modules\dol\components\Response;
use DateTime;
use DateTimeImmutable;
use Exception;

/**
 * Class SmsLogonResponse
 * @package app\modules\dol\components\v3\auth\smsLogOn
 *
 * smsCodeExpiration
 */
class SmsLogonResponse extends Response {
	/**
	 * @return DateTime
	 * @throws Exception
	 */
	public function getSmsCodeExpiredData():DateTime {
		return DateTimeImmutable::createFromMutable(
			new DateTime($this->data['smsCodeExpiration'])
		);
	}
}