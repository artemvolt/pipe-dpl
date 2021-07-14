<?php
declare(strict_types = 1);

namespace app\modules\dol\components\v3\auth\register;

use app\modules\dol\components\Response;

/**
 * Class RegisterResponse
 * @package app\modules\dol\components\v3\auth\register
 */
class RegisterResponse extends Response {
	/**
	 * @return string
	 */
	public function getVerificationToken():string {
		return $this->data['verificationToken'];
	}
}