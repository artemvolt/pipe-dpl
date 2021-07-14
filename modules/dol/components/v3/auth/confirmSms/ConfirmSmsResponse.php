<?php
declare(strict_types = 1);

namespace app\modules\dol\components\v3\auth\confirmSms;

use app\modules\dol\components\Response;
use app\modules\dol\models\DolAuthToken;
use DateTime;
use DateTimeImmutable;
use pozitronik\helpers\ArrayHelper;
use Throwable;

/**
 * Class ConfirmSmsResponse
 * @package app\modules\dol\components\v3\auth\confirmSms
 *
 * authToken {value, expires}
 */
class ConfirmSmsResponse extends Response {
	/**
	 * @return DolAuthToken
	 * @throws Throwable
	 */
	public function getAuthToken():DolAuthToken {
		return DolAuthToken::create(
			ArrayHelper::getValue($this->data, 'auth.accessToken.value'),
			DateTimeImmutable::createFromMutable(
				new DateTime(
					ArrayHelper::getValue($this->data, 'auth.accessToken.expires')
				)
			)
		);
	}
}