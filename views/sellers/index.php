<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var SellersSearch $searchModel
 * @var string $modelName
 * @var ControllerTrait $controller
 * @var ActiveDataProvider $dataProvider
 */

use app\assets\ModalHelperAsset;
use app\controllers\DealersController;
use app\controllers\StoresController;
use app\models\core\prototypes\ProjectConstants;
use app\models\countries\active_record\references\RefCountries;
use app\models\regions\active_record\references\RefRegions;
use app\models\seller\Sellers;
use app\models\seller\SellersSearch;
use app\modules\status\models\StatusRulesModel;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use kartik\select2\Select2;
use pozitronik\grid_config\GridConfig;
use pozitronik\helpers\Utils;
use pozitronik\traits\traits\ControllerTrait;
use pozitronik\widgets\BadgeWidget;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use app\controllers\UsersController;
use app\assets\ValidationAsset;

ModalHelperAsset::register($this);
ValidationAsset::register($this);
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
					'edit' => static function(string $url, SellersSearch $model) use ($modelName):string {
						return Html::a('<i class="fa fa-edit"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$modelName}-modal-edit-{$model->id}');event.preventDefault();")
						]);
					},
					'view' => static function(string $url, SellersSearch $model) use ($modelName):string {
						return Html::a('<i class="fa fa-eye"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$modelName}-modal-view-{$model->id}');event.preventDefault();")
						]);
					},
					'edit-user' => static function(string $url, SellersSearch $model) use ($modelName):string {
						return Html::a('<i class="fa fa-user-alt"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$modelName}-modal-edit-user-{$model->id}');event.preventDefault();")
						]);
					},
				],
			],
			'id',
			[
				'class' => DataColumn::class,
				'attribute' => 'currentStatus',
				'format' => 'raw',
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => ArrayHelper::map(StatusRulesModel::getAllStatuses(Sellers::class), 'id', 'name'),
				'filterWidgetOptions' => [
					'pluginOptions' => ['allowClear' => true, 'placeholder' => '']
				]
			],
			[
				'attribute' => 'userId',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relatedUser,
						'subItem' => 'id',
						'useBadges' => false,
						'urlScheme' => [
							UsersController::to(
								'index',
								['UsersSearch[id]' => $model->relatedUser->id??null]
							)
						]
					]);
				}
			],
			[
				'attribute' => 'userLogin',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relatedUser,
						'subItem' => 'login',
						'useBadges' => false
					]);
				}
			],
			[
				'attribute' => 'userEmail',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relatedUser,
						'subItem' => 'email',
						'useBadges' => false
					]);
				}
			],
			'surname',
			'name',
			'patronymic',
			[
				'attribute' => 'gender',
				'value' => static function(SellersSearch $model) {
					return ArrayHelper::getValue(ProjectConstants::GENDER, $model->gender);
				},
				'format' => 'raw',
				'filter' => ProjectConstants::GENDER,
				'filterWidgetOptions' => [
					'pluginOptions' => ['allowClear' => true, 'placeholder' => '']
				]
			],
			[
				'attribute' => 'birthday',
				'filterType' => DatePicker::class,
				'filterWidgetOptions' => [
					'type' => DatePicker::TYPE_INPUT,
					'pluginOptions' => [
						'alwaysShowCalendars' => true,
						'format' => 'yyyy-mm-dd'
					]
				]
			],
			[
				'attribute' => 'create_date',
				'filterType' => DateTimePicker::class,
				'filterWidgetOptions' => [
					'type' => DateTimePicker::TYPE_INPUT,
					'pluginOptions' => [
						'alwaysShowCalendars' => true
					]
				]
			],
			[
				'attribute' => 'update_date',
				'filterType' => DateTimePicker::class,
				'filterWidgetOptions' => [
					'type' => DateTimePicker::TYPE_INPUT,
					'pluginOptions' => [
						'alwaysShowCalendars' => true
					]
				]
			],
			[
				'attribute' => 'citizenName',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->refCountry,
						'subItem' => 'name'
					]);
				},
				'filter' => Select2::widget([
					'model' => $searchModel,
					'attribute' => 'citizen',
					'data' => RefCountries::mapData(),
					'pluginOptions' => [
						'allowClear' => true,
						'placeholder' => ''
					]
				])
			],
			[
				'attribute' => 'index',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relAddress,
						'subItem' => 'index',
						'useBadges' => false
					]);
				}
			],
			[
				'attribute' => 'areaName',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relAddress->refRegion??null,
						'subItem' => 'name'
					]);
				},
				'filter' => Select2::widget([
					'model' => $searchModel,
					'attribute' => 'area',
					'data' => RefRegions::mapData(),
					'pluginOptions' => [
						'allowClear' => true,
						'placeholder' => ''
					]
				])
			],
			[
				'attribute' => 'region',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relAddress,
						'subItem' => 'region',
						'useBadges' => false
					]);
				}
			],
			[
				'attribute' => 'city',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relAddress,
						'subItem' => 'city',
						'useBadges' => false
					]);
				}
			],
			[
				'attribute' => 'street',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relAddress,
						'subItem' => 'street',
						'useBadges' => false
					]);
				}
			],
			[
				'attribute' => 'building',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relAddress,
						'subItem' => 'building',
						'useBadges' => false
					]);
				}
			],
			[
				'attribute' => 'passport',
				'value' => static function(SellersSearch $model) {
					return "{$model->passport_series} {$model->passport_number}";
				}
			],
			'passport_whom',
			'passport_when',
			[
				'attribute' => 'entry_date',
				'filterType' => DatePicker::class,
				'filterWidgetOptions' => [
					'type' => DatePicker::TYPE_INPUT,
					'pluginOptions' => [
						'alwaysShowCalendars' => true,
						'format' => 'yyyy-mm-dd'
					]
				]
			],
			'keyword',
			'is_wireman_shpd:boolean',
			'inn',
			'snils',
			[
				'attribute' => 'store',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->stores,
						'subItem' => 'name',
						'urlScheme' => [StoresController::to('view'), 'id' => 'id'],
						'options' => static function($mapAttributeValue, $model):array {
							$url = StoresController::to('view', ['id' => $model->id]);
							return [
								'onclick' => new JsExpression("AjaxModal('$url', '{$model->formName()}-modal-view-{$model->id}');event.preventDefault();")
							];
						}
					]);
				}
			],
			[
				'attribute' => 'dealer',
				'format' => 'raw',
				'value' => static function(SellersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->dealers,
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