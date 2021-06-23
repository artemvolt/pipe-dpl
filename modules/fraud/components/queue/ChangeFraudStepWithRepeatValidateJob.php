<?php
declare(strict_types = 1);

namespace app\modules\fraud\components\queue;

use app\modules\fraud\components\FraudValidator;
use app\modules\fraud\components\ValidateWithChangeStep;
use app\modules\fraud\models\FraudCheckStepSearch;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class FraudValidatorRepeatJob
 * @package app\modules\fraud\components\queue
 */
class ChangeFraudStepWithRepeatValidateJob extends BaseObject implements JobInterface {
	public $fraudStepId;

	/**
	 * @param Queue $queue which pushed and is handling the job
	 * @return void result of the job execution
	 */
	public function execute($queue):void {
		/**
		 * @var FraudValidator $validator
		 */
		$fraudStep = (new FraudCheckStepSearch())->getById($this->fraudStepId);
		$validatorClass = $fraudStep->fraud_validator;
		$validator = new ValidateWithChangeStep(new $validatorClass());
		$validator->repeatValidate($this->fraudStepId);
	}
}
