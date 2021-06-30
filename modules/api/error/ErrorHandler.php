<?php
declare(strict_types = 1);

namespace app\modules\api\error;

use Error;
use Exception;
use Yii;
use yii\base\ErrorHandler as YiiErrorHandler;
use yii\base\UserException;
use yii\web\HttpException;

/**
 * Class ErrorHandler
 * @package app\modules\api\error
 */
class ErrorHandler extends YiiErrorHandler {
	/**
	 * @param Error|Exception $exception
	 */
	protected function renderException($exception):void {
		Yii::$app->response->setStatusCodeByException($exception);

		Yii::$app->response->data = $this->convertExceptionToArray($exception);
		Yii::$app->response->send();
	}

	/**
	 * @param Error|Exception $exception
	 * @return array
	 */
	protected function convertExceptionToArray($exception):array {
		if (!YII_DEBUG && !$exception instanceof UserException && !$exception instanceof HttpException) {
			$exception = new HttpException(500, 'An internal server error occurred.');
		}

		return [
			'error' => $exception instanceof HttpException?$exception->statusCode:$exception->getCode(),
			'error_description' => $exception->getMessage()
		];
	}
}