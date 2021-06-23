<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Rewards $model
 */

use app\models\reward\Rewards;
use pozitronik\widgets\BadgeWidget;
use yii\web\View;
use yii\widgets\DetailView;

?>

<?= DetailView::widget([
	'model' => $model,
	'attributes' => [
		'id',
		'create_date',
		'quantity',
		[
			'attribute' => 'operation',
			'format' => 'raw',
			'value' => static function(Rewards $model):string {
				return BadgeWidget::widget([
					'items' => $model->relatedOperations,
					'subItem' => 'name'
				]);
			}
		],
		[
			'attribute' => 'rule',
			'format' => 'raw',
			'value' => static function(Rewards $model):string {
				return BadgeWidget::widget([
					'items' => $model->relatedRules,
					'subItem' => 'name'
				]);
			}
		],
		'comment',
		'deleted:boolean'
	]
]) ?>
