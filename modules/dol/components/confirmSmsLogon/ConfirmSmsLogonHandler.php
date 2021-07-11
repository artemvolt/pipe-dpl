<?php
declare(strict_types = 1);

namespace app\modules\dol\components\confirmSmsLogon;

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
	 * @var BaseHandler $baseHandler
	 */
	protected $baseHandler;

	public function __construct() {
		$this->baseHandler = new BaseHandler();
	}

	/**
	 * @param Response $response
	 * @return mixed
	 * @throws ValidateServerErrors
	 * @throws ServerDomainError
	 */
	public function handle(Response $response):array {
		$content = $this->baseHandler->handle($response);
		$this->baseHandler->exist('success', $content);
		$this->baseHandler->exist('isTimeout', $content);
		if ($content['isTimeout']) {
			throw new ServerDomainError("Истекло время ожидания для подтверждение смс. Запросите повторно.");
		}
		return $content;
	}
}