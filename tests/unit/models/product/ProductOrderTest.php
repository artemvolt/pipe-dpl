<?php

namespace models\product;

use app\models\tests\product\ProductOrderTest as ProductTestModel;
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
use Codeception\Test\Unit;
use UnitTester;
use Yii;
use yii\helpers\ArrayHelper;

class ProductOrderTest extends Unit {
	/**
	 * @var UnitTester
	 */
	protected $tester;

	protected function _before() {
	}

	protected function _after() {
	}

	// tests
	public function testFraud() {
		$order = ProductTestModel::create(1, 2, 3)->saveAndReturn();
		$steps = FraudCheckStep::find()->asArray()->all();
		$this->assertCount(10, $steps);
		$this->assertEquals([
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
		], array_values(
				ArrayHelper::map($steps, 'fraud_validator', 'fraud_validator')
			)
		);

		Yii::$app->queue->executeJobs();
		$this->assertCount(
			0,
			FraudCheckStep::find()->andWhere(['status' => FraudCheckStep::STATUS_WAIT])->all()
		);
	}
}