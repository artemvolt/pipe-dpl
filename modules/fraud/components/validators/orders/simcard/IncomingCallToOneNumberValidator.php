<?php
declare(strict_types = 1);

namespace app\modules\fraud\components\validators\orders\simcard;

use app\modules\fraud\components\FraudValidator;

/**
 * Class IncomingCallToOneNumber
 * @package app\modules\fraud\components\validators\orders\simcard
 */
class IncomingCallToOneNumberValidator implements FraudValidator {

	/**
	 * @inheritDoc
	 */
	public function name():string {
		return "Проверка на совершение исходящих звонков на один номер телефона";
	}

	/**
	 * @inheritDoc
	 */
	public function validate(int $entityId):void {

	}
}
