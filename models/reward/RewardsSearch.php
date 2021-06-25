<?php
declare(strict_types = 1);

namespace app\models\reward;

use app\components\db\ActiveQuery;
use app\models\reward\active_record\RewardsAR;
use app\models\reward\config\RewardsOperationsConfig;
use app\models\reward\config\RewardsRulesConfig;
use app\modules\status\models\Status;
use app\modules\status\models\StatusRulesModel;
use pozitronik\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use app\models\sys\users\Users;
use Throwable;

/**
 * Class RewardsSearch
 * @property null|string $userName
 * @property null|string $ruleName
 * @property null|int $statusFilter
 * @property null|int $operationFilter
 * @property null|int $ruleFilter
 */
final class RewardsSearch extends RewardsAR {

	public ?string $userName = null;
	public ?string $ruleName = null;
	/*хотя фильтры номерные, данные приходят в строковом виде. Нормализуем в rules()*/
	public ?string $statusFilter = null;
	public ?string $operationFilter = null;
	public ?string $ruleFilter = null;

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['id', 'quantity', 'deleted', 'operation', 'statusFilter', 'operationFilter', 'ruleFilter'], 'integer'],
			['create_date', 'date', 'format' => 'php:Y-m-d H:i'],
			[['userName', 'ruleName'], 'string', 'max' => 255],
			[['statusFilter', 'operationFilter', 'ruleFilter'], 'filter', 'filter' => static function($value) {
				return ('' === $value || null === $value)?null:(int)$value;
			}],
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelStatus():ActiveQuery {
		return $this->hasOne(Status::class, [
			'model_key' => 'id'
		])->andOnCondition(['model_name' => Rewards::class]);
	}

	/**
	 * Для "таблиц", перечисляемых массивом key=>value, генерируем фильтр и его алиас,
	 * Так обеспечивается наполнение атрибута + алфавитная сортировка
	 * @param string $tableName Имя таблицы, содержащей ключ атрибута
	 * @param string $fieldName Имя поля, содержащего ключ атрибута
	 * @param array $dataArray Массив с перечислением атрибутов
	 * @param string $filterAlias Алиас, под которым задастся фильтр
	 * @return string
	 * todo: это полезная штука, куда-то в общее пространство её вытащить
	 *
	 * @throws InvalidConfigException
	 */
	private static function arrayFilter(string $tableName, string $fieldName, array $dataArray, string $filterAlias):string {
		$indexArray = [];//в массиве могут быть "дыры", их необходимо заполнить, чтобы ELT обращался к правильному значению
		$index = 1;
		foreach ($dataArray as $key => $value) {
			if ($key < $index) throw new InvalidConfigException('Ключи должны идти от 1 по возрастанию');

			if ($key > $index) {
				$emptyItemsCount = $key - $index;
				$indexArray = array_pad($indexArray, count($indexArray) + $emptyItemsCount, null);
			}
			$indexArray[] = $value;
			$index++;
		}

		$dataArrayStr = implode("', '", $indexArray);
		return "ELT({$tableName}.{$fieldName}, '{$dataArrayStr}') AS {$filterAlias}";
	}

	/**
	 * @param ActiveQuery $query
	 * @throws Throwable
	 */
	private function initQuery(ActiveQuery $query):void {
		$query->select([
			self::tableName().'.*',
			Users::tableName().'.username AS userName',
			self::arrayFilter(Status::tableName(), 'status', ArrayHelper::map(StatusRulesModel::getAllStatuses(Rewards::class), 'id', 'name'), 'statusFilter'),
			self::arrayFilter(self::tableName(), 'operation', RewardsOperationsConfig::mapData(), 'operationFilter'),
			self::arrayFilter(self::tableName(), 'rule', RewardsRulesConfig::mapData(), 'ruleFilter'),

			/*Так обеспечивается наполнение атрибута + алфавитная сортировка*/
		])
			->distinct()
			->active();
	}

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 * @throws Throwable
	 */
	public function search(array $params):ActiveDataProvider {
		$query = Rewards::find();
		$query->joinWith(['relStatus', 'relatedUser']);
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
	 */
	private function filterData($query):void {
		$query->andFilterWhere([self::tableName().'.id' => $this->id])
			->andFilterWhere([self::tableName().'.quantity' => $this->quantity])
			->andFilterWhere(['>=', self::tableName().'.create_date', $this->create_date])
			->andFilterWhere([Status::tableName().'.status' => $this->statusFilter])
			->andFilterWhere([self::tableName().'.operation' => $this->operationFilter])
			->andFilterWhere([self::tableName().'.rule' => $this->ruleFilter])
			->andFilterWhere(['like', Users::tableName().'.username', $this->userName])
			->andFilterWhere([self::tableName().'.deleted' => $this->deleted]);
	}

	/**
	 * @param $dataProvider
	 */
	private function setSort($dataProvider):void {
		$dataProvider->setSort([
			'defaultOrder' => ['id' => SORT_ASC],
			'attributes' => [
				'id',
				'quantity',
				'create_date',
				'deleted',
				'statusFilter' => [
					'asc' => ['statusFilter' => SORT_ASC],
					'desc' => ['statusFilter' => SORT_DESC]
				],
				'operationFilter' => [
					'asc' => ['operationFilter' => SORT_ASC],
					'desc' => ['operationFilter' => SORT_DESC]
				],
				'ruleFilter' => [
					'asc' => ['ruleFilter' => SORT_ASC],
					'desc' => ['ruleFilter' => SORT_DESC]
				],
				'userName' => [
					'asc' => [Users::tableName().'.username' => SORT_ASC],
					'desc' => [Users::tableName().'.username' => SORT_DESC]
				]
			],
		]);
	}

}