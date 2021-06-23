<?php
declare(strict_types = 1);

/**
 * @var HistoryEventAction[] $actions
 */

use app\modules\history\models\HistoryEventAction;
use yii\data\ArrayDataProvider;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\i18n\Formatter;

?>

<?= GridView::widget([
	'dataProvider' => new ArrayDataProvider([
		'allModels' => $actions,
		'sort' => [
			'attributes' => ['type', 'attributeName']
		]
	]),
	'summary' => false,
	'formatter' => [
		'class' => Formatter::class,
		'nullDisplay' => ''
	],
	'columns' => [
		[
			'class' => DataColumn::class,
			'attribute' => 'typeName'
		],
		[
			'class' => DataColumn::class,
			'enableSorting' => false,
			'attribute' => 'attributeName'
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'attributeOldValue',
			'value' => static function(HistoryEventAction $model) {
				return is_array($model->attributeOldValue)?json_encode($model->attributeOldValue, JSON_PRETTY_PRINT):$model->attributeOldValue;//fixme not json
			}
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'attributeNewValue',
			'value' => static function(HistoryEventAction $model) {
				return is_array($model->attributeNewValue)?json_encode($model->attributeNewValue, JSON_PRETTY_PRINT):$model->attributeNewValue;//fixme not json
			}
		]
	]
]) ?>
