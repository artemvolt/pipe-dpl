<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Stores $model
 */

use app\models\store\active_record\StoresAR;
use app\models\store\Stores;
use pozitronik\widgets\BadgeWidget;
use yii\web\View;
use yii\widgets\DetailView;

?>

<?= DetailView::widget([
	'model' => $model,
	'attributes' => [
		'id',
		'name',
		'create_date',
		[
			'attribute' => 'type',
			'format' => 'raw',
			'value' => static function(StoresAR $model):string {
				return BadgeWidget::widget([
					'items' => $model->refStoresTypes,
					'subItem' => 'name'
				]);
			}
		],
		[
			'attribute' => 'branches',
			'value' => static function(StoresAR $model) {
				return $model->refBranches->name??null;
			}
		],
		[
			'attribute' => 'regions',
			'value' => static function(StoresAR $model) {
				return $model->refRegions->name??null;
			}
		],
		[
			'attribute' => 'sellingChannels',
			'value' => static function(StoresAR $model) {
				return $model->refSellingChannels->name??null;
			}
		],
		[
			'attribute' => 'sellers',
			'format' => 'raw',
			'value' => static function(StoresAR $model):string {
				return BadgeWidget::widget([
					'items' => $model->sellers,
					'subItem' => 'fio'
				]);
			}
		],
		[
			'attribute' => 'managers',
			'format' => 'raw',
			'value' => static function(StoresAR $model):string {
				return BadgeWidget::widget([
					'items' => $model->managers,
					'subItem' => 'fio'
				]);
			}
		],
		[
			'attribute' => 'dealer',
			'format' => 'raw',
			'value' => static function(StoresAR $model):string {
				return BadgeWidget::widget([
					'items' => $model->dealer,
					'subItem' => 'name'
				]);
			}
		],
		'deleted:boolean'
	]
]) ?>
