<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Sellers $model
 * @var SellerMiniAssignWithStoreForm $assignForm
 * @var Stores[] $currentUserStores
 */

use app\assets\ValidationAsset;
use app\models\seller\SellerMiniAssignWithStoreForm;
use app\models\seller\Sellers;
use app\models\store\Stores;
use app\widgets\selectmodelwidget\SelectModelWidget;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\bootstrap4\ActiveForm;

ValidationAsset::register($this);
?>

<?php $form = ActiveForm::begin(['id' => 'assign-mini-with-store']); ?>
<div class="panel">
	<div class="panel-hdr">
	</div>
	<div class="panel-container show">
		<div class="panel-content">
			<div class="row">
				<div class="col-md-12">
					<?= $form->field($assignForm, 'store_id')->widget(SelectModelWidget::class, [
						'multiple' => false,
						'loadingMode' => SelectModelWidget::DATA_MODE_LOAD,
						'selectModelClass' => Stores::class,
						'options' => ['placeholder' => ''],
						'concatFields' => 'id, name',
						/**
						 * @TODO разобраться - в выпадающем списке, группы называется - 0
						 */
						'data' => [ArrayHelper::map($currentUserStores, 'id', 'name')],
					]) ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?= $form->field($assignForm, 'phone_number')->textInput() ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?= $form->field($assignForm, 'email')->textInput() ?>
				</div>
			</div>
		</div>
		<div class="panel-content">
			<?= Html::submitButton('Привязать', ['class' => 'btn btn-success float-right']) ?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
