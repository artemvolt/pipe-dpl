<?php
declare(strict_types = 1);

namespace app\modules\dol\components\v3\auth\confirmSms;

use app\modules\dol\components\Response;
use DateTime;
use DateTimeImmutable;
use Exception;

/**
 * Class ConfirmSmsResponse
 * @package app\modules\dol\components\v3\auth\confirmSms
 *
 * authToken {value, expires}
 */
class ConfirmSmsResponse extends Response {
	/**
	 * @return string
	 */
	public function getAuthTokenValue():string {
		return $this->data['authToken']['value'];
	}

	/**
	 * @return DateTime
	 * @throws Exception
	 */
	public function getAuthTokenExpires():DateTime {
		return DateTimeImmutable::createFromMutable(
			new DateTime($this->data['authToken']['expires'])
		);
	}
}