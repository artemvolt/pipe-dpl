<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var CreateSellerInviteLinkForm $model
 * @var ActiveForm $form
 */

use app\assets\ValidationAsset;
use app\controllers\StoresController;
use app\models\seller\invite_link\CreateSellerInviteLinkForm;
use app\models\store\Stores;
use app\widgets\selectmodelwidget\SelectModelWidget;
use kartik\form\ActiveForm;
use yii\web\View;

ValidationAsset::register($this);
?>

<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'store_id')->widget(SelectModelWidget::class, [
			'multiple' => false,
			'loadingMode' => SelectModelWidget::DATA_MODE_AJAX,
			'selectModelClass' => Stores::class,
			'options' => ['placeholder' => ''],
			'ajaxSearchUrl' => StoresController::to('ajax-search'),
			'pluginOptions' => [
				'disabled' => !$model->isNewRecord
			]
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'phone_number')->textInput(['placeholder' => '9123456789']) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'email')->textInput() ?>
	</div>
</div>