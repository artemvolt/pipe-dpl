<?php
declare(strict_types = 1);

namespace app\modules\history\models;

use app\modules\history\models\active_record\History;
use Throwable;
use yii\data\ActiveDataProvider;

/**
 * Class HistorySearch
 */
class HistorySearch extends History {
	public $actions;
	public $username;

	/**
	 * {@inheritDoc}
	 */
	public function rules():array {
		return [
			[['actions', 'user', 'at', 'model_class'], 'safe']
		];
	}

	/**
	 * @param array $params
	 * @param bool $pagination
	 * @return ActiveDataProvider
	 * @throws Throwable
	 */
	public function search(array $params, bool $pagination = true):ActiveDataProvider {
		$query = ActiveRecordHistory::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		$dataProvider->setSort([
			'defaultOrder' => ['id' => SORT_DESC],
			'attributes' => [
				'id',
				'at',
				'modelKey',
				'modelClass'
			]
		]);

		$this->load($params);
		if (false === $pagination) $dataProvider->pagination = $pagination;

		if (!$this->validate()) return $dataProvider;

		$query->andFilterWhere(['in', 'modelClass', $this->model_class]);

		return $dataProvider;
	}

}