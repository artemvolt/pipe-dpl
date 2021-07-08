<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var CreateSellerInviteLinkForm $createForm
 */

use app\controllers\StoresController;
use app\models\seller\invite_link\CreateSellerInviteLinkForm;
use app\models\store\Stores;
use app\widgets\selectmodelwidget\SelectModelWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap4\ActiveForm;

?>

<?php $form = ActiveForm::begin(['id' => 'create-seller-invite-link']); ?>
<div class="panel">
	<div class="panel-hdr">
	</div>
	<div class="panel-container show">
		<div class="panel-content">
			<div class="row">
				<div class="col-md-12">
					<?= $form->field($createForm, 'store_id')->widget(SelectModelWidget::class, [
						'multiple' => false,
						'loadingMode' => SelectModelWidget::DATA_MODE_AJAX,
						'selectModelClass' => Stores::class,
						'options' => ['placeholder' => ''],
						'ajaxSearchUrl' => StoresController::to('ajax-search')
					]) ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?= $form->field($createForm, 'phone_number')->textInput(['placeholder' => '9123456789']) ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?= $form->field($createForm, 'email')->textInput() ?>
				</div>
			</div>
		</div>
		<div class="panel-content">
			<?= Html::submitButton('Пригласить', [
					'class' => 'btn btn-primary float-right'
				]
			) ?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
