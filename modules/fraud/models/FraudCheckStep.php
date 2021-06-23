<?php
declare(strict_types = 1);

namespace app\modules\fraud\models;

use app\modules\fraud\models\active_record\FraudCheckStepAr;
use DomainException;
use pozitronik\helpers\DateHelper;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * Фродовая проверка
 *
 * Class FraudCheckStep
 * @package app\modules\fraud\models
 */
class FraudCheckStep extends FraudCheckStepAr {
	public const STATUS_SUCCESS = 1;
	public const STATUS_WAIT = 2;
	public const STATUS_PROCESS = 3;
	public const STATUS_FAIL = 4;

	public static array $statusesWithNames = [
		self::STATUS_SUCCESS => 'Фрод не найден',
		self::STATUS_WAIT => 'Ожидает проверки',
		self::STATUS_PROCESS => 'Обрабатывается',
		self::STATUS_FAIL => 'Фрод найден'
	];

	/**
	 * @param int $productOrderId
	 * @param string $entityClass
	 * @param string $fraudValidatorClass
	 * @return static //fixme: так self или static?
	 */
	public static function newStep(
		int $productOrderId,
		string $entityClass,
		string $fraudValidatorClass
	):self {
		$new = new self();
		$new->entity_id = $productOrderId;
		$new->entity_class = $entityClass;
		$new->fraud_validator = $fraudValidatorClass;
		$new->status = self::STATUS_WAIT;
		$new->updated_at = DateHelper::lcDate();
		$new->created_at = $new->updated_at;
		return $new;
	}

	/**
	 * @return string|null
	 */
	public function getStatusName():?string {
		return ArrayHelper::getValue(self::$statusesWithNames, $this->status);
	}

	/**
	 * @param array $steps
	 * @throws Exception
	 */
	public function addNewSteps(array $steps):void {
		$insertRows = array_map(static function(FraudCheckStep $step) {
			return array_values($step->toArray());
		}, $steps);

		$insertedRows = Yii::$app->db->createCommand()->batchInsert(self::tableName(),
			['entity_id', 'entity_class', 'fraud_validator', 'status', 'created_at', 'updated_at'],
			$insertRows
		)->execute();
		if ($insertedRows !== count($insertRows)) {
			throw new DomainException("Не получилось вставить все записи");
		}
	}

	/**
	 * @return $this
	 */
	public function statusSuccess():self {
		$this->status = self::STATUS_SUCCESS;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function statusProcess():self {
		$this->status = self::STATUS_PROCESS;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function statusFail():self {
		$this->status = self::STATUS_FAIL;
		return $this;
	}

	/**
	 * @param string $message
	 * @return FraudCheckStep
	 */
	public function addFraudMessage(string $message):self {
		$result = $this->step_info;
		$result['fraud_message'] = $message;
		$this->step_info = $result;
		return $this;
	}
}
