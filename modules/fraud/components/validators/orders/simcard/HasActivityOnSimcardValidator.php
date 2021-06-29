<?php
/** @noinspection PhpUnusedParameterInspection todo: remove after implementation! */
declare(strict_types = 1);

namespace app\modules\fraud\components\validators\orders\simcard;

use app\modules\fraud\components\FraudException;
use app\modules\fraud\components\FraudValidator;

/**
 * Class HasActivityOnSimcard
 * @package app\modules\fraud\components\validators\orders\simcard
 */

class HasActivityOnSimcardValidator implements FraudValidator {

	/**
	 * @inheritDoc
	 */
	public function name():string {
		return 'Есть активность по симке(звонки, интернет)';
	}

	/**
	 * @inheritDoc
	 */
	public function validate(int $entityId):void {
		$has = $this->hasIncomingCalls(5, 15) || $this->hasOutgoingCalls(5, 15) || $this->hasInternetTraffic(5, 15);
		if (!$has) {
			throw new FraudException("Нет активности");
		}
	}

	/**
	 * @param int $minutes
	 * @param int $days
	 * @return bool
	 */
	protected function hasIncomingCalls(int $minutes, int $days):bool {
		return true;
	}

	/**
	 * @param int $minutes
	 * @param int $days
	 * @return bool
	 */
	protected function hasOutgoingCalls(int $minutes, int $days):bool {
		return false;
	}

	/**
	 * @param int $MB
	 * @param int $days
	 * @return bool
	 */
	protected function hasInternetTraffic(int $MB, int $days):bool {
		return true;
	}
}
