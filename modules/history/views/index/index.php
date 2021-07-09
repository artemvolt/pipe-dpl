<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var HistorySearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use app\controllers\UsersController;
use app\modules\history\models\ActiveRecordHistory;
use app\modules\history\models\HistoryEventInterface;
use app\modules\history\models\HistorySearch;
use kartik\datetime\DateTimePicker;
use pozitronik\widgets\BadgeWidget;
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\web\View;

?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'summary' => false,
	'showOnEmpty' => false,
	'formatter' => [
		'class' => Formatter::class,
		'nullDisplay' => ''
	],
	'columns' => [
		'id',
		[
			'attribute' => 'user',
			'format' => 'raw',
			'value' => static function(ActiveRecordHistory $model):string {
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
			'attribute' => 'event',
			'value' => static function(ActiveRecordHistory $model) {
				return $model->historyEvent->eventCaption;
			},
			'format' => 'raw',
			'filter' => HistoryEventInterface::EVENT_TYPE_FRAMEWORK_NAMES,
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true, 'placeholder' => '']
			]
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'tag',
		],
		[
			'attribute' => 'at',
			'filterType' => DateTimePicker::class,
			'filterWidgetOptions' => [
				'type' => DateTimePicker::TYPE_INPUT,
				'pluginOptions' => [
					'alwaysShowCalendars' => true
				]
			]
		],
		[
			'attribute' => 'model_class',
			'value' => static function(ActiveRecordHistory $model) {
				return null === $model->model_key?$model->model_class:Html::a($model->model_class, ['show', 'for' => $model->model_class, 'id' => $model->model_key]);
			},
			'format' => 'raw',
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'relation_model',
			'format' => 'raw',
		],
		[
			'attribute' => 'model_key',
			'value' => static function(ActiveRecordHistory $model) {
				return null === $model->model_key?$model->model_key:Html::a($model->model_key, ['history', 'for' => $model->model_class, 'id' => $model->model_key]);
			},
			'format' => 'raw'

		],
		[
			'attribute' => 'actions',
			'filter' => false,
			'format' => 'raw',
			'value' => static function(ActiveRecordHistory $model) {
				return $model->historyEvent->timelineEntry->content;
			}
		],
		'scenario',
		'delegate'
	]
]) ?>

