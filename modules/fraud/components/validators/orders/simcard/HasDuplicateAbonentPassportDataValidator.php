<?php
declare(strict_types = 1);

namespace app\modules\fraud\components\validators\orders\simcard;

use app\modules\fraud\components\FraudValidator;

/**
 * Class CheckDuplicateAbonentPassportData
 * @package app\modules\fraud\components\validators\orders\simcard
 */
class HasDuplicateAbonentPassportDataValidator implements FraudValidator {

	/**
	 * @inheritDoc
	 */
	public function name():string {
		return "Проверка на корректность паспорт данных при продаже тарифа (дубли, есть ли абоненты с теми же паспортными данными, но с различными ФИО).";
	}

	/**
	 * @inheritDoc
	 */
	public function validate(int $entityId):void {

	}
}
