<?php
declare(strict_types = 1);

namespace app\modules\fraud\components\validators\orders\simcard;

use app\modules\fraud\components\FraudException;
use app\modules\fraud\components\FraudValidator;

/**
 * Class CheckOnDecreaseTariffPlan
 * @package app\modules\fraud\components\validators\orders\simcard
 */
class HasDecreaseTariffPlanValidator implements FraudValidator {

	/**
	 * @inheritDoc
	 */
	public function name():string {
		return "Проверка на соответствие тарифного плана в течение Х дней (проверка на понижение тарифа)";
	}

	/**
	 * @inheritDoc
	 */
	public function validate(int $entityId):void {
		throw new FraudException("Зафиксировано понижение тарифа");
	}
}
