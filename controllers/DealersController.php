<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\core\prototypes\DefaultController;
use app\models\branches\active_record\references\RefBranches;
use app\models\dealers\active_record\references\RefDealersGroups;
use app\models\dealers\active_record\references\RefDealersTypes;
use app\models\dealers\Dealers;
use app\models\dealers\DealersSearch;
use app\models\sys\permissions\filters\PermissionFilter;
use yii\helpers\ArrayHelper;

/**
 * Управление дилерами
 */
class DealersController extends DefaultController {

	public string $modelClass = Dealers::class;
	public string $modelSearchClass = DealersSearch::class;

	public array $mappingRules = [
		/*номер столбца => правило сопоставления*/
		0 => null, /*skip*/
		1 => [
			'attribute' => 'group',//куда вставить значение
			'foreign' => [//какая-то связь
				'class' => RefDealersGroups::class,//с чем
				'attribute' => 'name',//в какое поле вставить значение
				'key' => 'id'//что получить. Можно не указывать, тогда используется primaryKey
			],
		],
		2 => [
			'attribute' => 'branch',
			'foreign' => [
				'class' => RefBranches::class,
				'attribute' => 'name',
				'key' => 'id'
			]
		],
		3 => [
			'attribute' => 'type',
			'foreign' => [
				'class' => RefDealersTypes::class,
				'attribute' => 'name',
				'key' => 'id'
			]
		],
		4 => ['attribute' => 'name'],
		5 => ['attribute' => 'code'],
		6 => ['attribute' => 'client_code']
	];

	/**
	 * @inheritDoc
	 */
	public function behaviors():array {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => PermissionFilter::class
			]
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function getViewPath():string {
		return '@app/views/dealers';
	}

}