<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var EditSellerInviteLink $editForm
 * @var SellerInviteLink $existentModel
 */

use app\models\seller\invite_link\EditSellerInviteLink;
use app\models\seller\SellerInviteLink;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap4\ActiveForm;
?>

<?php $form = ActiveForm::begin(['id' => 'edit-seller-invite-link']); ?>
<div class="panel">
	<div class="panel-hdr">
	</div>
	<div class="panel-container show">
		<div class="panel-content">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label>Магазин</label>
						<?= Html::textInput("store_id", $existentModel->store->name, ['disabled' => true, 'class' => 'form-control']) ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?= $form->field($editForm, 'phone_number')->textInput(['placeholder' => '9123456789']) ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?= $form->field($editForm, 'email')->textInput() ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?= $form->field($editForm, 'repeatPhoneNotify')->checkbox() ?>
					<?= $form->field($editForm, 'repeatEmailNotify')->checkbox() ?>
				</div>
			</div>
		</div>
		<div class="panel-content">
			<?= Html::submitButton('Обновить', [
					'class' => 'btn btn-primary float-right'
				]
			) ?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
