<?php
declare(strict_types = 1);

namespace app\modules\fraud\models;

use app\modules\fraud\models\active_record\FraudCheckStepAr;
use DomainException;
use Throwable;
use yii\data\ActiveDataProvider;

/**
 * Class FraudCheckStepSearch
 * @package app\modules\fraud\models
 */
class FraudCheckStepSearch extends FraudCheckStepAr {
	/**
	 * @param array $params
	 * @param bool $pagination
	 * @return ActiveDataProvider
	 * @throws Throwable
	 */
	public function search(array $params, bool $pagination = true):ActiveDataProvider {
		$query = FraudCheckStep::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		$dataProvider->setSort([
			'defaultOrder' => ['id' => SORT_DESC],
			'attributes' => [
				'id',
				'entity_id',
				'fraud_validator',
				'status'
			]
		]);

		$this->load($params);
		if (false === $pagination) {
			$dataProvider->pagination = $pagination;
		}

		//if (!$this->validate()) return $dataProvider;

		$query->andFilterWhere(['=', 'status', $this->status]);

		return $dataProvider;
	}

	/**
	 * @param int $entityId
	 * @param string $validatorClass
	 * @return FraudCheckStep
	 * @throws DomainException
	 */
	public function getByEntityIdWithValidator(int $entityId, string $validatorClass):FraudCheckStep {
		if (null === $step = FraudCheckStep::find()
				->andWhere(['entity_id' => $entityId, 'fraud_validator' => $validatorClass])
				->andWhere(['status' => FraudCheckStep::STATUS_WAIT])
				->orderBy('created_at DESC')
				->limit(1)
				->one()) {
			throw new DomainException("Не найдена запись фродовой проверки.");
		}
		return $step;
	}

	public function getById(int $id):FraudCheckStep {
		if (null !== $find = FraudCheckStep::findOne(['id' => $id])) {
			return $find;
		}
		throw new DomainException("Не найдена запись фродовой проверку по id. ID: $id");
	}
}
