<?php
declare(strict_types = 1);

namespace app\modules\dol\components;

/**
 * Class Response
 * @package app\modules\dol\components
 */
abstract class Response {
	protected array $data;

	public function __construct(array $data) {
		$this->data = $data;
	}
}