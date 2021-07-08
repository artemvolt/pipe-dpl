<?php
declare(strict_types = 1);

namespace app\models\seller;

use DomainException;
use InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

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
	 * @return ActiveDataProvider
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
	 * @return SellerInviteLink
	 */
	public function getById(int $id):SellerInviteLink {
		if ($find = SellerInviteLink::findOne($id)) {
			return $find;
		}
		throw new DomainException("Не получилось найти ссылку по id");
	}

	/**
	 * @param string $phoneNumber
	 * @return SellerInviteLink|null
	 */
	public function findByPhone(string $phoneNumber):?SellerInviteLink {
		return SellerInviteLink::find()->where(['phone_number' => $phoneNumber])->one();
	}

	/**
	 * @param string $email
	 * @return SellerInviteLink|null
	 */
	public function findByEmail(string $email):?SellerInviteLink {
		return SellerInviteLink::find()->where(['email' => $email])->one();
	}

	/**
	 * @param string|null $email
	 * @param string|null $phone
	 * @return SellerInviteLink|null
	 */
	public function findExistentWithEmailOrPhone(?string $email = "", ?string $phone = ""):?SellerInviteLink {
		if (empty($email) || empty($phone)) {
			throw new InvalidArgumentException("Должен быть хотя бы указан либо email либо телефон");
		}
		$query = SellerInviteLink::find();
		if ($email) {
			$query->orWhere(['email' => $email]);
		}
		if ($phone) {
			$query->orWhere(['phone' => $phone]);
		}
		return $query->limit(1)->all();
	}

	/**
	 * @param string $token
	 * @return SellerInviteLink|null
	 */
	public function findByValidToken(string $token):?SellerInviteLink {
		return SellerInviteLink::find()->where(['token' => $token])
			->andWhere(['>=', 'expired_at', new Expression("NOW()")])
			->limit(1)
			->one();
	}
}
