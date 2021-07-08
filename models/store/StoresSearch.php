<?php
declare(strict_types = 1);

namespace app\models\store;

use app\components\db\ActiveQuery;
use app\models\branches\active_record\references\RefBranches;
use app\models\dealers\Dealers;
use app\models\managers\Managers;
use app\models\regions\active_record\references\RefRegions;
use app\models\seller\Sellers;
use app\models\store\active_record\references\RefSellingChannels;
use app\models\store\active_record\references\RefStoresTypes;
use app\models\store\active_record\StoresAR;
use DomainException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Throwable;

/**
 * Class StoresSearch
 * @property null|string $seller
 * @property null|string $manager
 * @property null|string $dealerSearch
 * @property null|string $typeName
 * @property null|string $regionName
 * @property null|string $sellingChannelName
 * @property null|string $branchName
 */
final class StoresSearch extends StoresAR {

	public ?string $seller = null;
	public ?string $dealerSearch = null;
	public ?string $manager = null;
	public ?string $typeName = null;
	public ?string $regionName = null;
	public ?string $sellingChannelName = null;
	public ?string $branchName = null;

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['id', 'name', 'seller', 'manager', 'dealerSearch'], 'filter', 'filter' => 'trim'],
			[['id', 'deleted', 'type', 'region', 'branch', 'selling_channel'], 'integer'],
			[['name', 'seller', 'manager', 'dealerSearch'], 'string', 'max' => 255]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return ArrayHelper::merge(parent::attributeLabels(), [
			'dealerSearch' => 'Дилер',
			'seller' => 'Продавец',
			'manager' => 'Менеджер',
			'typeName' => 'Тип',
			'regionName' => 'Регион',
			'branchName' => 'Филиал',
			'sellingChannelName' => 'Канал продаж',
		]);
	}

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 * @throws Throwable
	 */
	public function search(array $params):ActiveDataProvider {
		$query = self::find()->distinct()->active();
		$query->scope();
		$query->joinWith([
			'sellers',
			'managers',
			'dealer',
			'refStoresTypes',
			'refRegions',
			'refSellingChannels',
			'refBranches'
		]);
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
			->andFilterWhere([self::tableName().'.type' => $this->type])
			->andFilterWhere([self::tableName().'.region' => $this->region])
			->andFilterWhere([self::tableName().'.branch' => $this->branch])
			->andFilterWhere([self::tableName().'.selling_channel' => $this->selling_channel])
			->andFilterWhere([self::tableName().'.deleted' => $this->deleted])
			->andFilterWhere(['like', RefStoresTypes::tableName().'.name', $this->typeName])
			->andFilterWhere(['like', Sellers::tableName().'.surname', $this->seller])
			->andFilterWhere(['like', Managers::tableName().'.surname', $this->manager])
			->andFilterWhere(['like', Dealers::tableName().'.name', $this->dealerSearch]);
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
				'deleted',
				'typeName' => [
					'asc' => [RefStoresTypes::tableName().'.name' => SORT_ASC],
					'desc' => [RefStoresTypes::tableName().'.name' => SORT_DESC]
				],
				'branchName' => [
					'asc' => [RefBranches::tableName().'.name' => SORT_ASC],
					'desc' => [RefBranches::tableName().'.name' => SORT_DESC]
				],
				'regionName' => [
					'asc' => [RefRegions::tableName().'.name' => SORT_ASC],
					'desc' => [RefRegions::tableName().'.name' => SORT_DESC]
				],
				'sellingChannelName' => [
					'asc' => [RefSellingChannels::tableName().'.name' => SORT_ASC],
					'desc' => [RefSellingChannels::tableName().'.name' => SORT_DESC]
				],
				'dealerSearch' => [
					'asc' => [Dealers::tableName().'.name' => SORT_ASC],
					'desc' => [Dealers::tableName().'.name' => SORT_DESC]
				]
			],
		]);
	}

	/**
	 * @param ActiveQuery $query
	 * @throws Throwable
	 */
	private function initQuery(ActiveQuery $query):void {
		$query->select(
			[
				self::tableName().'.*',
				RefStoresTypes::tableName().'.name  AS typeName',
				RefRegions::tableName().'.name  AS regionName',
				RefBranches::tableName().'.name  AS branchName',
				RefSellingChannels::tableName().'.name  AS sellingChannelsName',
				Dealers::tableName().'.name  AS dealerSearch'
			]
		);
	}

	/**
	 * @param int $storeId
	 * @return Stores
	 */
	public function getById(int $storeId):Stores {
		if ($find = Stores::findOne($storeId)) {
			return $find;
		}
		throw new DomainException("Не удалось найти торговую точку по id");
	}
}
