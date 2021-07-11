<?php
declare(strict_types = 1);

namespace app\modules\dol\components;

use app\modules\dol\components\exceptions\ServerDomainError;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use RuntimeException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\httpclient\Response;

/**
 * Class Handler
 * @package app\modules\dol\components
 */
class BaseHandler {
	/**
	 * @param Response $response
	 * @return array
	 * @throws ValidateServerErrors
	 * @throws ServerDomainError
	 */
	public function handle(Response $response):array {
		if (null === $content = json_decode($response->content, true, 512, JSON_OBJECT_AS_ARRAY)) {
			throw new RuntimeException('Не получилось распознать ответ от сервера');
		}
		if ($errors = ArrayHelper::getValue($content, 'errors', [])) {
			throw new ValidateServerErrors($errors, 'Ошибка валидации на стороне сервиса');
		}
		if ($errorMessage = ArrayHelper::getValue($content, 'errorMessage')) {
			throw new ServerDomainError(Html::encode($errorMessage));
		}
		return $content;
	}

	/**
	 * @param string $key
	 * @param array $content
	 */
	public function exist(string $key, array $content):void {
		if (!array_key_exists($key, $content)) {
			throw new RuntimeException("Неправильный ответ сервера. Ключ {$key} не найден.");
		}
	}
}