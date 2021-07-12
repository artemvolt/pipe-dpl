<?php
declare(strict_types = 1);

namespace app\modules\dol\components\confirmSmsLogon;

use app\modules\dol\components\BaseHandler;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use RuntimeException;
use yii\httpclient\Response;

/**
 * Class SmsLogonHandler
 * @package app\modules\dol\components\confirmSmsLogon
 */
class SmsLogonHandler {
	protected $handler;

	public function __construct() {
		$this->handler = new BaseHandler();
	}

	/**
	 * @param Response $response
	 * @return array
	 * @throws ValidateServerErrors
	 */
	public function handle(Response $response):array {
		$content = $this->handler->handle($response);
		$this->handler->existKeyInResponse('success', $content);
		$this->handler->existKeyInResponse('smsCodeExpiration', $content);
		if (!$content['success']) {
			throw new RuntimeException("Ожидалось другое поведение");
		}
		return $content;
	}
}