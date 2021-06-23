<?php
declare(strict_types = 1);

namespace app\modules\fraud\components\validators\orders\simcard;

use app\modules\fraud\components\FraudValidator;

/**
 * Class IsActiveSimcardValidator
 * @package app\modules\fraud\components\validators\orders\simcard
 */
class IsActiveSimcardValidator implements FraudValidator {

	/**
	 * @inheritDoc
	 */
	public function name():string {
		return 'Активность симкарты';
	}

	/**
	 * @inheritDoc
	 */
	public function validate(int $entityId):void {

	}
}

