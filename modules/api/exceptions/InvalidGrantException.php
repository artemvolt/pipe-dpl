<?php
declare(strict_types = 1);

namespace app\modules\api\exceptions;

use Throwable;
use yii\base\UserException;

/**
 * Class InvalidGrantException
 * @package app\modules\api\exceptions
 */
class InvalidGrantException extends UserException {
	/**
	 * InvalidGrantException constructor.
	 * @param Throwable|null $previous
	 */
	public function __construct(Throwable $previous = null) {
		parent::__construct('Invalid or expired refresh token.', 0, $previous);

		$this->code = 'ERR_GRANT';
	}
}