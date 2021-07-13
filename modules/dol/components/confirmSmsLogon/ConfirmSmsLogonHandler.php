<?php
declare(strict_types = 1);

namespace app\modules\dol\components\confirmSmsLogon;

use app\modules\dol\components\exceptions\NotSuccessError;
use app\modules\dol\components\exceptions\ServerDomainError;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use app\modules\dol\components\BaseHandler;
use yii\httpclient\Response;

/**
 * Class ConfirmSmsLogonHandler
 * @package app\modules\dol\components\confirmSmsLogon
 */
class ConfirmSmsLogonHandler {

	/**
	 * @param Response $response
	 * @return mixed
	 * @throws ValidateServerErrors
	 * @throws ServerDomainError
	 */
	public function handle(Response $response):array {
		$content = BaseHandler::handle($response);
		BaseHandler::existKeyInResponse('success', $content);
		BaseHandler::existKeyInResponse('isTimeout', $content);
		if ($content['isTimeout']) {
			throw new ServerDomainError("Истекло время ожидания для подтверждение смс. Запросите повторно.");
		}
		if (!$content['success']) {
			throw new NotSuccessError($content['errorMessage']??'Ожидалось другое поведение');
		}
		return $content;
	}
}
