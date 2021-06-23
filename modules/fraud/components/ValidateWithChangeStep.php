<?php
declare(strict_types = 1);

namespace app\modules\fraud\components;

use app\modules\fraud\models\FraudCheckStep;
use app\modules\fraud\models\FraudCheckStepSearch;

/**
 * Обертка для валидаторов, после валидации
 * меняем статусы у записи фродовой проверки в бд
 *
 * Class ValidateWithChangeStep
 * @package app\modules\fraud\components
 */
class ValidateWithChangeStep {

	private FraudValidator $validator;

	/**
	 * ValidateWithChangeStep constructor.
	 * @param FraudValidator $validator
	 */
	public function __construct(FraudValidator $validator) {
		$this->validator = $validator;
	}

	/**
	 * @param int $entityId
	 */
	public function validate(int $entityId):void {
		$validatorOrderRow = (new FraudCheckStepSearch())->getByEntityIdWithValidator($entityId, get_class($this->validator));
		$this->validateWithCatch($validatorOrderRow);
	}

	/**
	 * @param int $stepId
	 */
	public function repeatValidate(int $stepId):void {
		$step = (new FraudCheckStepSearch())->getById($stepId);
		$this->validateWithCatch($step);
	}

	/**
	 * @param FraudCheckStep $step
	 */
	protected function validateWithCatch(FraudCheckStep $step):void {
		try {
			$this->validator->validate($step->entity_id);
			$step->statusSuccess()->saveAndReturn();
		} catch (FraudException $e) {
			$step->addFraudMessage($e->getMessage());
			$step->statusFail()->saveAndReturn();
		}
	}
}
