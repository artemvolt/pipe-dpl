<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var RewardsSearch $searchModel
 * @var string $modelName
 * @var ControllerTrait $controller
 * @var ActiveDataProvider $dataProvider
 */

use app\assets\ModalHelperAsset;
use app\models\reward\config\RewardsOperationsConfig;
use app\models\reward\Rewards;
use app\models\reward\RewardsSearch;
use app\modules\status\models\StatusRulesModel;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use pozitronik\core\traits\ControllerTrait;
use pozitronik\grid_config\GridConfig;
use pozitronik\helpers\ArrayHelper;
use pozitronik\helpers\Utils;
use pozitronik\widgets\BadgeWidget;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\web\JsExpression;
use yii\web\View;

ModalHelperAsset::register($this);
?>
<?= GridConfig::widget([
	'id' => "{$modelName}-index-grid",
	'grid' => GridView::begin([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'panel' => [
			'heading' => $this->title.(($dataProvider->totalCount > 0)
					?" (".Utils::pluralForm($dataProvider->totalCount, ['запись', 'записи', 'записей']).")"
					:" (нет записей)"),
		],
		'summary' => null !== $searchModel?Html::a('Новая запись', $controller::to('create'), [
			'class' => 'btn btn-success',
			'onclick' => new JsExpression("AjaxModal('".$controller::to('create').
				"', '{$modelName}-modal-create-new');event.preventDefault();")
		]):null,
		'showOnEmpty' => true,
		'emptyText' => Html::a('Новая запись', $controller::to('create'), [
			'class' => 'btn btn-success',
			'onclick' => new JsExpression("AjaxModal('".$controller::to('create').
				"', '{$modelName}-modal-create-new');event.preventDefault();")
		]),
		'export' => false,
		'resizableColumns' => true,
		'responsive' => true,
		'columns' => [
			[
				'class' => ActionColumn::class,
				'template' => '{edit}{view}{why}',
				'buttons' => [
					'edit' => static function(string $url, Rewards $model) use ($modelName) {
						return Html::a('<i class="fa fa-edit"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', ".
								"'{$modelName}-modal-edit-{$model->id}');event.preventDefault();")
						]);
					},
					'view' => static function(string $url, Rewards $model) use ($modelName) {
						return Html::a('<i class="fa fa-eye"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', ".
								"'{$modelName}-modal-view-{$model->id}');event.preventDefault();")
						]);
					},
					'why' => static function(string $url, Rewards $model) {
						return Html::a('<i class="fa fa-lightbulb-dollar"></i>', $url, [
							'onclick' => new JsExpression("alert('todo: будем показывать причинно-следственные связи начисления');event.preventDefault();")
						]);
					},
				],
			],
			'id',
			[
				'attribute' => 'operationFilter',
				'label' => 'Действие',
				'format' => 'raw',
				'value' => static function(Rewards $model):string {
					return BadgeWidget::widget([
						'items' => $model->relatedOperations,
						'subItem' => 'name',
						'useBadges' => false
					]);
				},
				'filter' => Select2::widget([
					'model' => $searchModel,
					'attribute' => 'operationFilter',
					'data' => RewardsOperationsConfig::mapData(),
					'pluginOptions' => [
						'allowClear' => true,
						'placeholder' => ''
					]
				])
			],
			[
				'format' => 'raw',
				'attribute' => 'reason',
				'value' => static function(Rewards $model):string {
					return BadgeWidget::widget([
						'items' => ArrayHelper::getValue(Rewards::reasons(), $model->reason)
					]);
				}
			],
			'quantity',
			'waiting',
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
				'attribute' => 'userName',
				'label' => 'Получатель',
				'format' => 'raw',
				'value' => static function(Rewards $model):string {
					return BadgeWidget::widget([
						'items' => $model->relatedUser,
						'subItem' => 'username',
						'useBadges' => false
					]);
				}
			],
			[
				'class' => DataColumn::class,
				'label' => 'Статус',
				'attribute' => 'statusFilter',
				'value' => static function(Rewards $model):string {
					return BadgeWidget::widget([
						'items' => $model->currentStatus,
						'subItem' => 'name',
						'useBadges' => false
					]);
				},
				'format' => 'raw',
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => ArrayHelper::map(StatusRulesModel::getAllStatuses(Rewards::class), 'id', 'name'),
				'filterWidgetOptions' => [
					'pluginOptions' => ['allowClear' => true, 'placeholder' => '']
				]
			],
			[
				'attribute' => 'ruleFilter',
				'label' => 'Правило',
				'format' => 'raw',
				'value' => static function(Rewards $model):string {
					return BadgeWidget::widget([
						'items' => $model->relatedRules,
						'subItem' => 'name',
						'useBadges' => false
					]);
				}
			],
			'comment',
			'deleted:boolean'
		]
	])
]) ?>