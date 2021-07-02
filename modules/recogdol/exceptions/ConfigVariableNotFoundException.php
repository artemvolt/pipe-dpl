<?php
declare(strict_types = 1);

namespace app\modules\recogdol\exceptions;

use Exception;

/**
 * Class ConfigVariableNotFoundException
 * @package app\modules\recogdol\exceptions;
 */
class ConfigVariableNotFoundException extends Exception {
	/**
	 * UnauthorizedException constructor.
	 * @param string $message
	 */
	public function __construct(string $message = 'Some config variable not found') {
		parent::__construct($message, 0, null);
	}
}
