<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\models\seller\SellerInviteLink;
use DomainException;
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
		$query = SellerInviteLink::find();

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

	/**
	 * @return array
	 */
	public function all():array {
		return SellerInviteLink::find()->all();
	}

	/**
	 * @param int $id
	 * @return \app\models\seller\SellerInviteLink
	 */
	public function getById(int $id):SellerInviteLink {
		if ($find = SellerInviteLink::findOne($id)) {
			return $find;
		}
		throw new DomainException("Не получилось найти ссылку по id");
	}

	/**
	 * @param string $phoneNumber
	 * @return \app\models\seller\SellerInviteLink|null
	 */
	public function findByPhone(string $phoneNumber):?SellerInviteLink {
		return SellerInviteLink::find()->where(['phone_number' => $phoneNumber])->one();
	}

	/**
	 * @param $email
	 * @return \app\models\seller\SellerInviteLink|null
	 */
	public function findByEmail($email):?SellerInviteLink {
		return SellerInviteLink::find()->where(['email' => $email])->one();
	}
}
