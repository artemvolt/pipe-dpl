<?php
declare(strict_types = 1);

namespace app\modules\fraud\components\queue;

use app\modules\fraud\components\FraudValidator;
use app\modules\fraud\components\ValidateWithChangeStep;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class FraudValidatorJob
 * @package app\modules\fraud\components\queue
 */
class ChangeFraudStepWithValidateJob extends BaseObject implements JobInterface {

	/**
	 * @var int $entityId
	 */
	public $entityId;
	/**
	 * @var string $validatorClass
	 */
	public $validatorClass;

	/**
	 * @param Queue $queue which pushed and is handling the job
	 * @return void result of the job execution
	 */
	public function execute($queue):void {
		/**
		 * @var FraudValidator $validator
		 */
		$validator = new ValidateWithChangeStep(new $this->validatorClass());
		$validator->validate($this->entityId);
	}
}
