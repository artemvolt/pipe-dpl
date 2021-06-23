<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Managers $model
 */

use app\models\managers\Managers;
use pozitronik\widgets\BadgeWidget;
use yii\web\View;
use yii\widgets\DetailView;

?>

<?= DetailView::widget([
	'model' => $model,
	'attributes' => [
		'create_date',
		'update_date',
		[
			'attribute' => 'userId',
			'value' => static function(Managers $model) {
				return $model->relatedUser->id??null;
			}
		],
		[
			'attribute' => 'userLogin',
			'value' => static function(Managers $model) {
				return $model->relatedUser->login??null;
			}
		],
		[
			'attribute' => 'userEmail',
			'value' => static function(Managers $model) {
				return $model->relatedUser->email??null;
			}
		],
		'surname',
		'name',
		'patronymic',
		[
			'attribute' => 'stores',
			'format' => 'raw',
			'value' => static function(Managers $model):string {
				return BadgeWidget::widget([
					'items' => $model->stores,
					'subItem' => 'name'
				]);
			}
		],
		[
			'attribute' => 'dealers',
			'format' => 'raw',
			'value' => static function(Managers $model):string {
				return BadgeWidget::widget([
					'items' => $model->dealers,
					'subItem' => 'name'
				]);
			}
		],
		'deleted:boolean'
	]
]) ?>
