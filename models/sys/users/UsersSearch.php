<?php
declare(strict_types = 1);

namespace app\models\sys\users;

use app\models\sys\users\active_record\Users as ActiveRecordUsers;
use yii\data\ActiveDataProvider;

/**
 * Class UsersSearch
 */
class UsersSearch extends ActiveRecordUsers {

	/**
	 * @inheritdoc
	 */
	public function rules():array {
		return [
			[['id'], 'integer'],
			[['username', 'login', 'email'], 'safe'],
		];
	}

	/**
	 * @param array $params
	 * @param int[] $allowedGroups
	 * @return ActiveDataProvider
	 */
	public function search(array $params, array $allowedGroups = []):ActiveDataProvider {
		$query = Users::find()->active();

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		$dataProvider->setSort([
			'defaultOrder' => ['id' => SORT_ASC],
			'attributes' => [
				'id',
				'username',
				'login',
				'email',
			]
		]);

		$this->load($params);

		if (!$this->validate()) return $dataProvider;

		$query->distinct();

		$query->andFilterWhere(['sys_users.id' => $this->id])
			->andFilterWhere(['group_id' => $allowedGroups])
			->andFilterWhere(['like', 'sys_users.username', $this->username])
			->andFilterWhere(['like', 'login', $this->login])
			->andFilterWhere(['like', 'email', $this->email]);

		return $dataProvider;
	}
}
