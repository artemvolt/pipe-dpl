<?php
declare(strict_types = 1);

namespace app\modules\fraud\components;

/**
 * Interface FraudValidator
 * @package app\modules\fraud\components
 */
interface FraudValidator {
	/**
	 * Название валидатора
	 * @return string
	 */
	public function name():string;

	/**
	 * Например, ID заказа
	 * @param int $entityId
	 */
	public function validate(int $entityId):void;
}
