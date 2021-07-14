<?php
declare(strict_types = 1);

namespace app\modules\dol\components\v3\auth\smsLogOn;

use app\modules\dol\components\BaseHandler;
use app\modules\dol\components\exceptions\NotSuccessError;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use yii\httpclient\Response;

/**
 * Class SmsLogonHandler
 * @package app\modules\dol\components\confirmSmsLogon
 */
class SmsLogonHandler {

	/**
	 * @param Response $response
	 * @return array
	 * @throws ValidateServerErrors
	 */
	public function handle(Response $response):array {
		$content = BaseHandler::handleWithErrors($response);
		BaseHandler::existKeyInResponse('success', $content);
		BaseHandler::existKeyInResponse('smsCodeExpiration', $content);
		if (!$content['success']) {
			throw new NotSuccessError('Ожидалось другое поведение');
		}
		return $content;
	}
}
