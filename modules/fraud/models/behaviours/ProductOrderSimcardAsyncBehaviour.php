<?php
declare(strict_types = 1);

namespace app\modules\fraud\models\behaviours;

use app\models\products\ProductOrder;
use app\modules\fraud\components\queue\ChangeFraudStepWithValidateJob;
use app\modules\fraud\components\validators\orders\simcard\HasActivitySimcardWithOneBaseStationValidator;
use app\modules\fraud\components\validators\orders\simcard\HasDuplicateAbonentPassportDataValidator;
use app\modules\fraud\components\validators\orders\simcard\HasDecreaseTariffPlanValidator;
use app\modules\fraud\components\validators\orders\simcard\HasActivityOnSimcardValidator;
use app\modules\fraud\components\validators\orders\simcard\HasIncreaseBalanceValidator;
use app\modules\fraud\components\validators\orders\simcard\HasPaySubscriptionFeeAndHasntCallsValidator;
use app\modules\fraud\components\validators\orders\simcard\IncomingCallFromOneDeviceValidator;
use app\modules\fraud\components\validators\orders\simcard\IncomingCallToOneNumberValidator;
use app\modules\fraud\components\validators\orders\simcard\IsAbonentBlockByFraudValidator;
use app\modules\fraud\components\validators\orders\simcard\IsActiveSimcardValidator;
use app\modules\fraud\models\FraudCheckStep;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use Yii;
use yii\db\AfterSaveEvent;
use yii\db\Exception;

/**
 * Class ProductOrderSimcardAsyncBehaviour
 * @package app\modules\fraud\components\behaviours
 */
class ProductOrderSimcardAsyncBehaviour extends Behavior {
	public array $validators = [
		IsActiveSimcardValidator::class,
		HasActivityOnSimcardValidator::class,
		HasDecreaseTariffPlanValidator::class,
		IncomingCallToOneNumberValidator::class,
		IncomingCallFromOneDeviceValidator::class,
		HasDuplicateAbonentPassportDataValidator::class,
		HasIncreaseBalanceValidator::class,
		HasPaySubscriptionFeeAndHasntCallsValidator::class,
		IsAbonentBlockByFraudValidator::class,
		HasActivitySimcardWithOneBaseStationValidator::class
	];

	/**
	 * @inheritDoc
	 */
	public function events():array {
		return [
			ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
		];
	}

	/**
	 * @param AfterSaveEvent $event
	 * @throws Exception
	 */
	public function afterInsert(AfterSaveEvent $event):void {
		/**
		 * @var ActiveRecord $model
		 */
		$model = $event->sender;
		if (!($model instanceof ProductOrder && $model->isSimcard())) {
			return;
		}

		(new FraudCheckStep())->addNewSteps(array_map(static function($class) use ($event) {
			return FraudCheckStep::newStep($event->sender->id, get_class($event->sender), $class);
		}, $this->validators));

		foreach ($this->validators as $validatorClass) {
			Yii::$app->queue->push(new ChangeFraudStepWithValidateJob([
				'validatorClass' => $validatorClass,
				'entityId' => $event->sender->id
			]));
		}
	}
}
