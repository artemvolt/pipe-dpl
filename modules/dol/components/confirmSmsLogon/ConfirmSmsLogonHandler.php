<?php
declare(strict_types = 1);

namespace app\modules\dol\components\confirmSmsLogon;

use app\modules\dol\components\exceptions\ServerDomainError;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use app\modules\dol\components\BaseHandler;
use RuntimeException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
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
	 */
	public function handle(Response $response):array {
		$content = $this->baseHandler->handle($response);
		if ($errors = ArrayHelper::getValue($content, 'errors', [])) {
			throw new ValidateServerErrors($errors, 'Ошибка валидации на стороне сервиса');
		}
		if ($errorMessage = ArrayHelper::getValue($content, 'errorMessage')) {
			throw new ServerDomainError(Html::encode($errorMessage));
		}
		$this->exist('success', $content);
		$this->exist('isTimeout', $content);
		if ($content['isTimeout']) {
			throw new ServerDomainError("Истекло время ожидания для подтверждение смс. Запросите повторно.");
		}
		return $content;
	}

	/**
	 * @param string $key
	 * @param array $content
	 */
	protected function exist(string $key, array $content):void {
		if (!array_key_exists($key, $content)) {
			throw new RuntimeException("Неправильный ответ сервера. Ключ {$key} не найден.");
		}
	}
}