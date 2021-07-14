<?php
declare(strict_types = 1);

namespace app\modules\dol\components\v3\auth\register;

use app\modules\dol\components\BaseHandler;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use yii\httpclient\Response;

/**
 * Class CheckCodeHandler
 * @package app\modules\dol\components\v3\auth\register
 */
class CheckCodeHandler {
	/**
	 * @param Response $response
	 * @throws ValidateServerErrors
	 */
	public function handle(Response $response):void {
		BaseHandler::handleWithErrors($response);
	}
}