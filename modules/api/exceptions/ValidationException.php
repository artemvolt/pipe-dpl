<?php
declare(strict_types = 1);

namespace app\modules\api\exceptions;

use Throwable;
use yii\base\UserException;

/**
 * Class ValidationException
 * @package app\modules\api\exceptions
 */
class ValidationException extends UserException {
	/**
	 * ValidationException constructor.
	 * @param string $message
	 * @param Throwable|null $previous
	 */
	public function __construct($message = "", Throwable $previous = null) {
		parent::__construct($message, 0, $previous);

		$this->code = 'ERR_VALIDATION';
	}
}