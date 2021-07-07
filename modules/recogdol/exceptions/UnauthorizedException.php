<?php
declare(strict_types = 1);

namespace app\modules\recogdol\exceptions;

use Exception;

/**
 * Class UnauthorizedException
 * @package app\modules\recogdol\exceptions;
 */
class UnauthorizedException extends Exception {
	/**
	 * UnauthorizedException constructor.
	 * @param string $message
	 */
	public function __construct(string $message = 'Authorization in RecogDol not passed') {
		parent::__construct($message);
	}
}
