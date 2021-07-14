<?php
declare(strict_types = 1);

namespace app\modules\dol\components\v3\auth\register;

use app\modules\dol\components\BaseHandler;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use yii\httpclient\Response;

/**
 * Class RegisterHandler
 * @package app\modules\dol\components\v3\auth\register
 */
class RegisterHandler {
	/**
	 * @param Response $response
	 * @throws ValidateServerErrors
	 */
	public function handle(Response $response):void {
		$content = BaseHandler::handleWithErrors($response);
		BaseHandler::existKeyInResponse('verificationToken', $content);
	}
}