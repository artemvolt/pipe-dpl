<?php
declare(strict_types = 1);

namespace app\modules\dol\components\confirmSmsLogon;

use app\modules\dol\components\BaseHandler;
use app\modules\dol\components\exceptions\ValidateServerErrors;
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
		$this->handler->exist('success', $content);
		$this->handler->exist('smsCodeExpiration', $content);
		return $content;
	}
}