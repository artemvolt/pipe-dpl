<?php
declare(strict_types = 1);

namespace app\modules\fraud\components\validators\orders\simcard;

use app\modules\fraud\components\FraudValidator;

/**
 * Class HasActivitySimcardWithInOneBaseStationValidator
 * @package app\modules\fraud\components\validators\orders\simcard
 */
class HasActivitySimcardWithOneBaseStationValidator implements FraudValidator {

	/**
	 * @inheritDoc
	 */
	public function name():string {
		return "Проверка на активность сим-карты в течение Х дней в рамках одной базовой станции ";
	}

	/**
	 * @inheritDoc
	 */
	public function validate(int $entityId):void {

	}
}
