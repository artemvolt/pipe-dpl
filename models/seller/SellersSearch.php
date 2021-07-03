<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\models\addresses\Addresses;
use app\models\countries\active_record\references\RefCountries;
use app\components\db\ActiveQuery;
use app\models\dealers\Dealers;
use app\models\regions\active_record\references\RefRegions;
use app\models\store\Stores;
use app\models\sys\users\Users;
use app\modules\status\models\Status;
use app\modules\status\models\StatusRulesModel;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Throwable;

/**
 * Class StoresSearch
 * @property null|string $store
 * @property null|string $dealer
 *
 * @property null|string $passportExplodedSeries
 * @property null|string $passportExplodedNumber
 * @property null|string $userId
 * @property null|string $userLogin
 * @property null|string $userEmail
 * @property null|string $currentStatus
 * @property null|string $citizenName
 * @property null|string $index
 * @property null|string $region
 * @property null|string $area
 * @property null|string $areaName
 * @property null|string $city
 * @property null|string $street
 * @property null|string $building
 */
final class SellersSearch extends Sellers {

	public ?string $userId = null;
	public ?string $userEmail = null;
	public ?string $userLogin = null;
	public ?string $store = null;
	public ?string $dealer = null;
	public ?string $passport = null;
	public ?string $currentStatus = null;
	public ?string $citizenName = null;
	public ?string $index = null;
	public ?string $area = null;
	public ?string $areaName = null;
	public ?string $region = null;
	public ?string $city = null;
	public ?string $street = null;
	public ?string $building = null;

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[
				[
					'id', 'name', 'surname', 'patronymic', 'passport', 'keyword', 'birthday', 'entry_date',
					'create_date', 'update_date', 'store', 'userEmail', 'userLogin', 'userId', 'inn', 'snils', 'dealer',
					'index', 'region', 'city', 'street', 'building'
				],
				'filter',
				'filter' => 'trim'
			],
			[['id', 'userId', 'gender', 'currentStatus', 'citizen', 'index', 'area'], 'integer'],
			[['deleted', 'is_wireman_shpd'], 'boolean'],
			[['store', 'dealer', 'userEmail', 'region', 'city', 'street', 'building'], 'string', 'max' => 255],
			['userLogin', 'string', 'max' => 64],
			['userEmail', 'email'],
			[['birthday', 'entry_date'], 'date', 'format' => 'php:Y-m-d'],
			[['create_date', 'update_date'], 'date', 'format' => 'php:Y-m-d H:i'],
			[['keyword'], 'string', 'max' => 64],
			[['name', 'surname', 'patronymic', 'passport'], 'string', 'max' => 128],
			['inn', 'string', 'length' => 12],
			['inn', 'integer'],
			[
				'snils',
				'match',
				'pattern' => '/^(\d{3}\-\d{3}-\d{3} \d{2})$/',
				'message' => 'Значение «СНИЛС» неверно. Формат: 000-000-000 00'
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return ArrayHelper::merge(parent::attributeLabels(), [
			'passport' => 'Паспорт',
			'currentStatus' => 'Статус',
			'store' => 'Магазин',
			'dealer' => 'Дилер',
			'citizenName' => 'Гражданство',
			'index' => 'Индекс',
			'areaName' => 'Область',
			'region' => 'Регион/район',
			'city' => 'Город/н.п.',
			'street' => 'Улица',
			'building' => 'Дом'
		]);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelStatus():ActiveQuery {
		return $this->hasOne(Status::class, [
			'model_key' => 'id'
		])->andOnCondition(['model_name' => Sellers::class]);
	}

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 * @throws Throwable
	 */
	public function search(array $params):ActiveDataProvider {
		$query = self::find()->distinct()->active();
		$query->joinWith(['relStatus', 'stores', 'dealers', 'relatedUser', 'refCountry', 'relAddress', 'refRegion']);
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
			->andFilterWhere(['like', self::tableName().'.surname', $this->surname])
			->andFilterWhere(['like', self::tableName().'.patronymic', $this->patronymic])
			->andFilterWhere([self::tableName().'.gender' => $this->gender])
			->andFilterWhere([self::tableName().'.birthday' => $this->birthday])
			->andFilterWhere(['>=', self::tableName().'.create_date', $this->create_date])
			->andFilterWhere(['>=', self::tableName().'.update_date', $this->update_date])
			->andFilterWhere([self::tableName().'.citizen' => $this->citizen])
			->andFilterWhere([self::tableName().'.passport_series' => $this->passportExplodedSeries])
			->andFilterWhere([self::tableName().'.passport_number' => $this->passportExplodedNumber])
			->andFilterWhere([self::tableName().'.entry_date' => $this->entry_date])
			->andFilterWhere([self::tableName().'.keyword' => $this->keyword])
			->andFilterWhere([self::tableName().'.is_wireman_shpd' => $this->is_wireman_shpd])
			->andFilterWhere([self::tableName().'.inn' => $this->inn])
			->andFilterWhere([self::tableName().'.snils' => $this->snils])
			->andFilterWhere([self::tableName().'.deleted' => $this->deleted])
			->andFilterWhere(['like', Stores::tableName().'.name', $this->store])
			->andFilterWhere([Users::tableName().'.id' => $this->userId])
			->andFilterWhere([Users::tableName().'.email' => $this->userEmail])
			->andFilterWhere([Users::tableName().'.login' => $this->userLogin])
			->andFilterWhere([Status::tableName().'.status' => $this->currentStatus])
			->andFilterWhere([Addresses::tableName().'.index' => $this->index])
			->andFilterWhere([Addresses::tableName().'.area' => $this->area])
			->andFilterWhere([Addresses::tableName().'.region' => $this->region])
			->andFilterWhere([Addresses::tableName().'.city' => $this->city])
			->andFilterWhere([Addresses::tableName().'.street' => $this->street])
			->andFilterWhere([Addresses::tableName().'.building' => $this->building])
			->andFilterWhere(['like', Dealers::tableName().'.name', $this->dealer]);

		$query->scope(Sellers::class, Users::Current());
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
				'surname',
				'patronymic',
				'gender',
				'birthday',
				'deleted',
				'create_date',
				'update_date',
				'entry_date',
				'keyword',
				'is_wireman_shpd',
				'inn',
				'snils',
				'userId' => [
					'asc' => [Users::tableName().'.id' => SORT_ASC],
					'desc' => [Users::tableName().'.id' => SORT_DESC]
				],
				'userLogin' => [
					'asc' => [Users::tableName().'.login' => SORT_ASC],
					'desc' => [Users::tableName().'.login' => SORT_DESC]
				],
				'userEmail' => [
					'asc' => [Users::tableName().'.email' => SORT_ASC],
					'desc' => [Users::tableName().'.email' => SORT_DESC]
				],
				'currentStatus' => [
					'asc' => ['currentStatus' => SORT_ASC],
					'desc' => ['currentStatus' => SORT_DESC]
				],
				'citizenName' => [
					'asc' => [RefCountries::tableName().'.name' => SORT_ASC],
					'desc' => [RefCountries::tableName().'.name' => SORT_DESC]
				],
				'index' => [
					'asc' => [Addresses::tableName().'.index' => SORT_ASC],
					'desc' => [Addresses::tableName().'.index' => SORT_DESC]
				],
				'areaName' => [
					'asc' => [RefRegions::tableName().'.name' => SORT_ASC],
					'desc' => [RefRegions::tableName().'.name' => SORT_DESC]
				],
				'region' => [
					'asc' => [Addresses::tableName().'.region' => SORT_ASC],
					'desc' => [Addresses::tableName().'.region' => SORT_DESC]
				],
				'city' => [
					'asc' => [Addresses::tableName().'.city' => SORT_ASC],
					'desc' => [Addresses::tableName().'.city' => SORT_DESC]
				],
				'street' => [
					'asc' => [Addresses::tableName().'.street' => SORT_ASC],
					'desc' => [Addresses::tableName().'.street' => SORT_DESC]
				],
				'building' => [
					'asc' => [Addresses::tableName().'.building' => SORT_ASC],
					'desc' => [Addresses::tableName().'.building' => SORT_DESC]
				]
			]
		]);
	}

	/**
	 * Searching passport field consists of series and number. This getter returns us series.
	 * @return mixed|false|string[]
	 */
	public function getPassportExplodedSeries() {
		$passportArray = explode(' ', $this->passport);
		return array_shift($passportArray);
	}

	/**
	 * Searching passport field consists of series and number. This getter returns us number.
	 * @return mixed|string|null
	 */
	public function getPassportExplodedNumber() {
		$passportArray = explode(' ', $this->passport);
		return array_pop($passportArray);
	}

	/**
	 * @param ActiveQuery $query
	 * @throws Throwable
	 */
	private function initQuery(ActiveQuery $query):void {
		$query->select([
			self::tableName().'.*',
			RefCountries::tableName().'.name  AS citizenName',
			Users::tableName().'.id AS userId',
			Users::tableName().'.login AS userLogin',
			Users::tableName().'.email AS userEmail',
			Addresses::tableName().'.index AS index',
			Addresses::tableName().'.region AS region',
			Addresses::tableName().'.city AS city',
			Addresses::tableName().'.street AS street',
			Addresses::tableName().'.building AS building',
			RefRegions::tableName().'.name AS areaName',
			/*Так обеспечивается наполнение атрибута + алфавитная сортировка*/
			"ELT(".Status::tableName().'.status'.", '".implode("','", ArrayHelper::map(
				StatusRulesModel::getAllStatuses(Sellers::class),
				'id',
				'name'
			))."') AS currentStatus"
		])
			->distinct()
			->active();
	}

	/**
	 * @return Sellers[]
	 */
	public function findAllRows():array {
		return Sellers::find()->all();
	}
}
