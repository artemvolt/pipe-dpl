<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Dealers $model
 */

use app\models\dealers\Dealers;
use pozitronik\widgets\BadgeWidget;
use yii\web\View;
use yii\widgets\DetailView;

?>

<?= DetailView::widget([
	'model' => $model,
	'attributes' => [
		'create_date',
		'name',
		'code',
		'client_code',
		[
			'attribute' => 'group',
			'value' => static function(Dealers $model) {
				return $model->refDealersGroups->name??null;
			}
		],
		[
			'attribute' => 'branch',
			'value' => static function(Dealers $model) {
				return $model->refBranches->name??null;
			}
		],
		[
			'attribute' => 'type',
			'value' => static function(Dealers $model) {
				return $model->refDealersTypes->name??null;
			}
		],
		[
			'attribute' => 'stores',
			'format' => 'raw',
			'value' => static function(Dealers $model):string {
				return BadgeWidget::widget([
					'items' => $model->stores,
					'subItem' => 'name'
				]);
			}
		],
		[
			'attribute' => 'managers',
			'format' => 'raw',
			'value' => static function(Dealers $model):string {
				return BadgeWidget::widget([
					'items' => $model->managers,
					'subItem' => 'fio'
				]);
			}
		],
		[
			'attribute' => 'sellers',
			'format' => 'raw',
			'value' => static function(Dealers $model):string {
				return BadgeWidget::widget([
					'items' => $model->sellers,
					'subItem' => 'fio'
				]);
			}
		],
		'deleted:boolean'
	]
]) ?>
