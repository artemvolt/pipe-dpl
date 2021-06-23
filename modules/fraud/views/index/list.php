<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var FraudCheckStepSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var Notifications[] $notifications
 */

use app\modules\fraud\components\FraudValidator;
use app\modules\fraud\FraudModule;
use app\modules\fraud\models\FraudCheckStep;
use app\modules\fraud\models\FraudCheckStepSearch;
use app\modules\notifications\models\Notifications;
use app\modules\notifications\widgets\notification_alert\NotificationAlertWidget;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\grid\ActionColumn;

?>
<?php foreach ($notifications as $notification): ?>
	<?= NotificationAlertWidget::widget([
		'type' => NotificationAlertWidget::TYPE_SUCCESS,
		'notification' => $notification,
	]) ?>
<?php endforeach; ?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'summary' => false,
	'showOnEmpty' => true,
	'columns' => [
		[
			'class' => ActionColumn::class,
			'template' => '{repeat-validate}',
			'buttons' => [
				'repeat-validate' => static function(string $url, FraudCheckStep $model) {
					return Html::a('<i class="fas fa-redo"></i>', FraudModule::to(['index/list']), [
						'class' => 'btn',
						'data-method' => 'POST',
						'data-params' => [
							'repeat_validate_id' => $model->id,
						]
					]);
				}
			],
		],
		[
			'attribute' => 'id',
			'header' => 'Заказ'
		],
		'entity_id',
		'entity_class',
		[
			'attribute' => 'fraud_validator',
			'value' => static function(FraudCheckStep $model) {
				if (!class_exists($model->fraud_validator)) {
					return 'Валидатор не найден.'.$model->fraud_validator;
				}

				/**
				 * @var FraudValidator $validator
				 */
				$validator = new $model->fraud_validator();
				return $validator->name();
			}
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'status',
			'format' => 'raw',
			'filter' => FraudCheckStep::$statusesWithNames,
			'value' => static function(FraudCheckStep $model) {
				return $model->getStatusName()?:"Статус не определен $model->status";
			}
		],
		[
			'attribute' => 'step_info',
			'value' => static function(FraudCheckStep $model) {
				return ArrayHelper::getValue($model->step_info, 'fraud_message');
			}
		]
	]
])
?>

