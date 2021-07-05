<?php
declare(strict_types = 1);

namespace app\modules\recogdol\exceptions;

use Exception;

/**
 * Class ConfigNotFoundException
 * @package app\modules\recogdol\exceptions;
 */
class ConfigNotFoundException extends Exception {
	/**
	 * ConfigNotFoundException constructor.
	 * @param string $message
	 */
	public function __construct(string $message = 'Not found config file for module recogdol in modules/recogdol/config/') {
		parent::__construct($message);
	}
}
