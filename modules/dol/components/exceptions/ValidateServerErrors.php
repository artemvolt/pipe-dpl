<?php
declare(strict_types = 1);

namespace app\modules\dol\components\exceptions;

use Exception;
use Throwable;
use yii\helpers\Html;

/**
 * Class ValidateServerErrors
 * @package app\modules\dol\components\exceptions
 */
class ValidateServerErrors extends Exception {
	protected array $errors = [];

	/**
	 * ValidateServerErrors constructor.
	 * @param array $errors
	 * @param string $message
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct(array $errors = [], $message = "", $code = 0, Throwable $previous = null) {
		$this->errors = $errors;
		parent::__construct($message, $code, $previous);
	}

	public function getErrors():array {
		return $this->errors;
	}

	public function getErrorsInOneRow():string {
		$result = "";
		foreach ($this->getErrors() as $field => $errors) {
			foreach ($errors as $error) {
				$result .= $field." ".Html::encode($error);
			}
		}
		return $result;
	}

	public function mapErrors(array $map):array {
		$errors = $this->getErrors();
		$result = [];
		foreach ($map as $field => $outField) {
			if (array_key_exists($field, $errors)) {
				$result[$outField] = $errors[$field];
			}
		}
		return $result;
	}
}
