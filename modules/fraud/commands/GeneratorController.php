<?php
declare(strict_types = 1);

namespace app\modules\fraud\commands;

use app\models\products\ProductOrder;
use app\modules\fraud\models\behaviours\ProductOrderSimcardAsyncBehaviour;
use app\modules\fraud\models\FraudCheckStep;
use pozitronik\helpers\DateHelper;
use yii\console\Controller;
use yii\db\Exception;

/**
 * Class GeneratorController
 * @package app\modules\fraud\commands
 */
class GeneratorController extends Controller {
	public function actionOrder():void {
		FraudCheckStep::deleteAll();
		$newOrder = new ProductOrder();
		$newOrder->initiator = 1;
		$newOrder->store = 2;
		$newOrder->status = 3;
		$newOrder->create_date = DateHelper::lcDate();
		$newOrder->saveAndReturn();
	}

	/**
	 * @throws Exception
	 */
	public function actionNewSteps():void {
		FraudCheckStep::deleteAll();

		$behaviour = new ProductOrderSimcardAsyncBehaviour();

		(new FraudCheckStep())->addNewSteps(array_map(static function(string $validatorClass) {
			return FraudCheckStep::newStep(random_int(1, 100), ProductOrder::class, $validatorClass);
		}, $behaviour->validators));
	}
}
