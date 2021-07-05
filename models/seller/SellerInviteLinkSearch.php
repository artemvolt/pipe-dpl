<?php
declare(strict_types = 1);

namespace app\models\sys\users;

use app\models\seller\active_record\SellerInviteLink;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;

/**
 * Class SellerInviteLinkSearch
 * @package app\models\sys\users
 */
class SellerInviteLinkSearch extends SellerInviteLink {

	/**
	 * @inheritdoc
	 */
	public function rules():array {
		return [
			[['id'], 'integer'],
			[['store_id', 'phone_number', 'email', 'expired_at', 'token'], 'safe'],
		];
	}

	/**
	 * @param array $params
	 * @param int[] $allowedGroups
	 * @return ActiveDataProvider
	 * @throws Throwable
	 * @throws ForbiddenHttpException
	 */
	public function search(array $params):ActiveDataProvider {
		$query = Users::find()->active()->scope();

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		$dataProvider->setSort([
			'defaultOrder' => ['id' => SORT_ASC],
			'attributes' => [
				'id',
				'store_id',
				'phone_number',
				'email',
				'expired_at',
				'token'
			]
		]);

		$this->load($params);

		if (!$this->validate()) return $dataProvider;

		$query->distinct();

		$query->andFilterWhere(['id' => $this->id]);

		return $dataProvider;
	}
}
