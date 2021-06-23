<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var HistorySearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use app\modules\history\models\ActiveRecordHistory;
use app\modules\history\models\HistorySearch;
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;
use yii\grid\GridView;
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
		[
			'attribute' => 'eventType',
			'value' => static function(ActiveRecordHistory $model) {
				return $model->historyEvent->eventCaption;
			},
			'format' => 'raw'
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'tag',
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'at',
			'value' => 'at'
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'model_class',
			'value' => static function(ActiveRecordHistory $model) {
				return null === $model->model_key?$model->model_class:Html::a($model->model_class, ['show', 'for' => $model->model_class, 'id' => $model->model_key]);
			},
			'format' => 'raw',
			'filter' => $searchModel->model_class

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
		]
	]
]) ?>

