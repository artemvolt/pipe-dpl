<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\branches\active_record\references\RefBranches;
use app\models\core\prototypes\DefaultController;
use app\models\regions\active_record\references\RefRegions;
use app\models\store\active_record\references\RefSellingChannels;
use app\models\store\active_record\references\RefStoresTypes;
use app\models\store\Stores;
use app\models\store\StoresSearch;

/**
 * Class StoresController
 */
class StoresController extends DefaultController {

	protected const DEFAULT_TITLE = "Магазины";

	public string $modelClass = Stores::class;
	public string $modelSearchClass = StoresSearch::class;
	public bool $enablePrototypeMenu = false;

	public function getMappingRules():array {
		return [
			/*номер столбца => правило сопоставления*/
			0 => [
				'attribute' => 'region',
				'foreign' => [
					'class' => RefRegions::class,
					'attribute' => 'name',//в какое поле вставить значение
					'key' => 'id'//что получить. Можно не указывать, тогда используется primaryKey
				]
			],
			1 => [
				'attribute' => 'branch',
				'foreign' => [
					'match' => static function(string $attributeValue) {
						if (null === $record = RefBranches::find()->where(['like', 'name', "%$attributeValue%", false])->one()) {
							return null;
						}
						/** @var RefBranches $record */
						return $record->id;
					},
					'class' => RefBranches::class,
					'attribute' => 'name',//в какое поле вставить значение
					'key' => 'id'//что получить. Можно не указывать, тогда используется primaryKey
				]
			],
			5 => ['attribute' => 'name'],
			6 => [
				'attribute' => 'type',
				'foreign' => [
					'class' => RefStoresTypes::class,
					'attribute' => 'name',//в какое поле вставить значение
					'key' => 'id'//что получить. Можно не указывать, тогда используется primaryKey
				]
			],
			7 => [
				'attribute' => 'selling_channel',
				'foreign' => [
					'class' => RefSellingChannels::class,
					'attribute' => 'name',//в какое поле вставить значение
					'key' => 'id'//что получить. Можно не указывать, тогда используется primaryKey
				]
			],

		];
	}

	/**
	 * @inheritDoc
	 */
	public function getViewPath():string {
		return '@app/views/stores';
	}

}