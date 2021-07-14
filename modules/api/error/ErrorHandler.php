<?php
declare(strict_types = 1);

namespace app\modules\api\error;

use app\components\exceptions\ValidateException;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use DomainException;
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
		$newException = $exception;
		if (!YII_DEBUG && !$newException instanceof UserException && !$newException instanceof HttpException) {
			$newException = new HttpException(500, 'An internal server error occurred.');
		}

		$result = [
			'error' => $newException instanceof HttpException?$newException->statusCode:$newException->getCode(),
			'error_description' => $newException->getMessage()
		];

		if ($exception instanceof ValidateServerErrors || $exception instanceof ValidateException) {
			$newException = new HttpException(421, "Ошибка валидации");
			$outputErrors = [];
			foreach ($exception->getErrors() as $field => $errorsField) {
				foreach ($errorsField as $fieldError) {
					$outputErrors[] = [
						'field' => $field,
						'description' => $fieldError
					];
				}
			}
			$result['errors'] = $outputErrors;
		}

		if ($exception instanceof DomainException) {
			$result['errors'] = [
				['field' => 'phone_number', 'description' => $exception->getMessage()]
			];
		}

		if (YII_DEBUG) {
			$result['debug']['trace'] = $newException->getTraceAsString();
		}
		return $result;
	}
}