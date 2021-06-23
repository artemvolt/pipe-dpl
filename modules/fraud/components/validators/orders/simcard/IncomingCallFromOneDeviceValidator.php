<?php
declare(strict_types = 1);

namespace app\modules\fraud\components\validators\orders\simcard;

use app\modules\fraud\components\FraudValidator;

/**
 * Class IncomingCallFromOneDevice
 * @package app\modules\fraud\components\validators\orders\simcard
 */
class IncomingCallFromOneDeviceValidator implements FraudValidator {

	/**
	 * @inheritDoc
	 */
	public function name():string {
		return "Проверка на совершение исходящих звонков по проданным тарифам с одного устройства";
	}

	/**
	 * @inheritDoc
	 */
	public function validate(int $entityId):void {

	}
}
