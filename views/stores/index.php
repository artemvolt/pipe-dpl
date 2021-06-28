<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Stores $searchModel
 * @var string $modelName
 * @var ControllerTrait $controller
 * @var ActiveDataProvider $dataProvider
 */

use app\assets\ModalHelperAsset;
use app\controllers\DealersController;
use app\controllers\ManagersController;
use app\controllers\SellersController;
use app\models\store\Stores;
use app\models\store\StoresSearch;
use kartik\grid\GridView;
use kartik\select2\Select2;
use pozitronik\grid_config\GridConfig;
use pozitronik\helpers\Utils;
use pozitronik\traits\traits\ControllerTrait;
use pozitronik\widgets\BadgeWidget;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\web\JsExpression;
use yii\web\View;
use app\models\branches\active_record\references\RefBranches;
use app\models\regions\active_record\references\RefRegions;
use app\models\store\active_record\references\RefSellingChannels;
use app\models\store\active_record\references\RefStoresTypes;

ModalHelperAsset::register($this);
?>
<?= GridConfig::widget([
	'id' => "{$modelName}-index-grid",
	'grid' => GridView::begin([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'panel' => [
			'heading' => $this->title.(($dataProvider->totalCount > 0)?" (".Utils::pluralForm($dataProvider->totalCount, ['запись', 'записи', 'записей']).")":" (нет записей)"),
		],
		'summary' => null !== $searchModel?Html::a('Новая запись', $controller::to('create'), [
			'class' => 'btn btn-success',
			'onclick' => new JsExpression("AjaxModal('".$controller::to('create')."', '{$modelName}-modal-create-new');event.preventDefault();")
		]):null,
		'showOnEmpty' => true,
		'emptyText' => Html::a('Новая запись', $controller::to('create'), [
			'class' => 'btn btn-success',
			'onclick' => new JsExpression("AjaxModal('".$controller::to('create')."', '{$modelName}-modal-create-new');event.preventDefault();")
		]),
		'export' => false,
		'resizableColumns' => true,
		'responsive' => true,
		'columns' => [
			[
				'class' => ActionColumn::class,
				'template' => '{edit}{view}',
				'buttons' => [
					'edit' => static function(string $url, StoresSearch $model) use ($modelName) {
						return Html::a('<i class="fa fa-edit"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$modelName}-modal-edit-{$model->id}');event.preventDefault();")
						]);
					},
					'view' => static function(string $url, StoresSearch $model) use ($modelName) {
						return Html::a('<i class="fa fa-eye"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$modelName}-modal-view-{$model->id}');event.preventDefault();")
						]);
					},
				],
			],
			'id',
			'name',
			[
				'attribute' => 'typeName',
				'format' => 'raw',
				'value' => static function(StoresSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->refStoresTypes,
						'subItem' => 'name'
					]);
				}
			],
			[
				'attribute' => 'typeName',
				'format' => 'raw',
				'value' => static function(StoresSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->refStoresTypes,
						'subItem' => 'name'
					]);
				},
				'filter' => Select2::widget([
					'model' => $searchModel,
					'attribute' => 'type',
					'data' => RefStoresTypes::mapData(),
					'pluginOptions' => [
						'allowClear' => true,
						'placeholder' => ''
					]
				])
			],
			[
				'attribute' => 'branchName',
				'format' => 'raw',
				'value' => static function(StoresSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->refBranches,
						'subItem' => 'name'
					]);
				},
				'filter' => Select2::widget([
					'model' => $searchModel,
					'attribute' => 'branch',
					'data' => RefBranches::mapData(),
					'pluginOptions' => [
						'allowClear' => true,
						'placeholder' => ''
					]
				])
			],
			[
				'attribute' => 'regionName',
				'format' => 'raw',
				'value' => static function(StoresSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->refRegions,
						'subItem' => 'name'
					]);
				},
				'filter' => Select2::widget([
					'model' => $searchModel,
					'attribute' => 'region',
					'data' => RefRegions::mapData(),
					'pluginOptions' => [
						'allowClear' => true,
						'placeholder' => ''
					]
				])
			],
			[
				'attribute' => 'sellingChannelName',
				'format' => 'raw',
				'value' => static function(StoresSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->refSellingChannels,
						'subItem' => 'name'
					]);
				},
				'filter' => Select2::widget([
					'model' => $searchModel,
					'attribute' => 'selling_channel',
					'data' => RefSellingChannels::mapData(),
					'pluginOptions' => [
						'allowClear' => true,
						'placeholder' => ''
					]
				])
			],
			[
				'attribute' => 'seller',
				'format' => 'raw',
				'value' => static function(StoresSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->sellers,
						'subItem' => 'fio',
						'urlScheme' => [SellersController::to('view'), 'id' => 'id'],
						'options' => static function($mapAttributeValue, $model):array {
							$url = SellersController::to('view', ['id' => $model->id]);
							return [
								'onclick' => new JsExpression("AjaxModal('$url', '{$model->formName()}-modal-view-{$model->id}');event.preventDefault();")
							];
						}
					]);
				}
			],
			[
				'attribute' => 'manager',
				'format' => 'raw',
				'value' => static function(StoresSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->managers,
						'subItem' => 'fio',
						'urlScheme' => [ManagersController::to('view'), 'id' => 'id'],
						'options' => static function($mapAttributeValue, $model):array {
							$url = ManagersController::to('view', ['id' => $model->id]);
							return [
								'onclick' => new JsExpression("AjaxModal('$url', '{$model->formName()}-modal-view-{$model->id}');event.preventDefault();")
							];
						}
					]);
				}
			],
			[
				'attribute' => 'dealerSearch',
				'format' => 'raw',
				'value' => static function(StoresSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->dealer,
						'subItem' => 'name',
						'urlScheme' => [DealersController::to('view'), 'id' => 'id'],
						'options' => static function($mapAttributeValue, $model):array {
							$url = DealersController::to('view', ['id' => $model->id]);
							return [
								'onclick' => new JsExpression("AjaxModal('$url', '{$model->formName()}-modal-view-{$model->id}');event.preventDefault();")
							];
						}
					]);
				}
			],
			'deleted:boolean'
		]
	])
]) ?>