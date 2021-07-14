<?php
declare(strict_types = 1);

namespace app\modules\dol\components\v3\auth\confirmSms;

use app\modules\dol\components\exceptions\NotSuccessError;
use app\modules\dol\components\exceptions\ServerDomainError;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use app\modules\dol\components\BaseHandler;
use yii\httpclient\Response;

/**
 * Class ConfirmSmsLogonHandler
 * @package app\modules\dol\components\confirmSmsLogon
 */
class ConfirmSmsHandler {

	/**
	 * @param Response $response
	 * @return mixed
	 * @throws ValidateServerErrors
	 * @throws ServerDomainError
	 */
	public function handle(Response $response):array {
		$content = BaseHandler::handleWithErrors($response);
		BaseHandler::existKeyInResponse('success', $content);
		BaseHandler::existKeyInResponse('isTimeout', $content);
		if ($content['isTimeout']) {
			throw new ServerDomainError("Истекло время ожидания для подтверждение смс. Запросите повторно.");
		}
		if (!$content['success']) {
			throw new NotSuccessError('Ожидалось другое поведение');
		}
		return $content;
	}
}
