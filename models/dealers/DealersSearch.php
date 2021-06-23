<?php
declare(strict_types = 1);

namespace app\models\dealers;

use app\models\branches\active_record\references\RefBranches;
use app\models\dealers\active_record\DealersAR;
use app\models\dealers\active_record\references\RefDealersGroups;
use app\models\dealers\active_record\references\RefDealersTypes;
use app\models\dealers\active_record\relations\RelDealersToStores;
use app\models\managers\Managers;
use app\models\seller\Sellers;
use app\models\store\Stores;
use app\models\sys\users\Users;
use pozitronik\core\models\LCQuery;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class StoresSearch
 * @property null|string $store
 * @property null|string $manager
 * @property null|string $seller
 */
final class DealersSearch extends DealersAR {

	public ?string $store = null;
	public ?string $manager = null;
	public ?string $seller = null;

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['id', 'client_code', 'code', 'name', 'store', 'manager', 'seller'], 'filter', 'filter' => 'trim'],
			[['id', 'deleted', 'type', 'group', 'branch'], 'integer'],
			[['name', 'store', 'manager', 'seller'], 'string', 'max' => 255],
			[['code'], 'string', 'max' => 4],
			[['client_code'], 'string', 'max' => 9]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return ArrayHelper::merge(parent::attributeLabels(), [
			'groupName' => 'Группа',
			'typeName' => 'Тип',
			'branchName' => 'Филиал',
			'seller' => 'Продавец',
			'store' => 'Магазин',
			'manager' => 'Менеджер'
		]);
	}

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 * @throws Throwable
	 */
	public function search(array $params):ActiveDataProvider {
		$query = self::find();
		$query->joinWith(['stores', 'managers', 'sellers', 'refDealersGroups', 'refDealersTypes', 'refBranches']);
		$this->initQuery($query);

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		$this->setSort($dataProvider);
		$this->load($params);

		if (!$this->validate()) return $dataProvider;

		$this->filterData($query);

		return $dataProvider;
	}

	/**
	 * @param $query
	 * @return void
	 * @throws Throwable
	 */
	private function filterData($query):void {
		$query->andFilterWhere([self::tableName().'.id' => $this->id])
			->andFilterWhere(['like', self::tableName().'.name', $this->name])
			->andFilterWhere([self::tableName().'.code' => $this->code])
			->andFilterWhere([self::tableName().'.group' => $this->group])
			->andFilterWhere([self::tableName().'.type' => $this->type])
			->andFilterWhere([self::tableName().'.branch' => $this->branch])
			->andFilterWhere([self::tableName().'.client_code' => $this->client_code])
			->andFilterWhere([self::tableName().'.deleted' => $this->deleted])
			->andFilterWhere(['like', Stores::tableName().'.name', $this->store])
			->andFilterWhere(['like', Managers::tableName().'.surname', $this->manager])
			->andFilterWhere(['like', Sellers::tableName().'.surname', $this->seller]);

		$this->filterDataByUser($query);
	}

	/**
	 * @param LCQuery $query
	 * @throws Throwable
	 */
	private function initQuery(LCQuery $query):void {
		$query->select([
			self::tableName().'.*',
			RefDealersGroups::tableName().'.name AS groupName',
			RefDealersTypes::tableName().'.name AS typeName',
			RefBranches::tableName().'.name AS branchName',
		])
			->distinct()
			->active();
	}

	/**
	 * Filters the records shown for current user
	 * @param $query
	 * @throws Throwable
	 */
	private function filterDataByUser($query):void {
		$user = Users::Current();
		if ($user->isAllPermissionsGranted()) {
			return;
		}
		$manager = Managers::findOne(['user' => $user->id]);
		if (null === $manager) {
			return;
		}

		if ($user->hasPermission(['dealer_managers'])) {
			$query->andFilterWhere(
				[
					'in',
					RelDealersToStores::tableName().'.dealer_id',
					ArrayHelper::getColumn($manager->relatedDealersToManagers, 'dealer_id')
				]
			);
		}
	}

	/**
	 * @param $dataProvider
	 */
	private function setSort($dataProvider):void {
		$dataProvider->setSort([
			'defaultOrder' => ['id' => SORT_ASC],
			'attributes' => [
				'id',
				'name',
				'code',
				'client_code',
				'typeName' => [
					'asc' => [RefDealersTypes::tableName().'.name' => SORT_ASC],
					'desc' => [RefDealersTypes::tableName().'.name' => SORT_DESC]
				],
				'groupName' => [
					'asc' => [RefDealersGroups::tableName().'.name' => SORT_ASC],
					'desc' => [RefDealersGroups::tableName().'.name' => SORT_DESC]
				],
				'branchName' => [
					'asc' => [RefBranches::tableName().'.name' => SORT_ASC],
					'desc' => [RefBranches::tableName().'.name' => SORT_DESC]
				],
				'deleted'
			]
		]);
	}
}