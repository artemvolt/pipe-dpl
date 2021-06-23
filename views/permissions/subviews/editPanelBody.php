<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Permissions $model
 * @var ActiveForm $form
 */

use app\models\core\TemporaryHelper;
use app\models\sys\permissions\Permissions;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use kartik\touchspin\TouchSpin;
use yii\web\View;

?>

<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'name')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'priority')->widget(TouchSpin::class) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'controller')->widget(Select2::class, [
			'data' => TemporaryHelper::GetControllersList(Permissions::ConfigurationParameter(Permissions::CONTROLLER_DIRS)),
			'pluginOptions' => [
				'multiple' => false,
				'allowClear' => true,
				'placeholder' => '',
				'tags' => true
			]
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'action')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'verb')->widget(Select2::class, [
			'data' => TemporaryHelper::VERBS,
			'pluginOptions' => [
				'multiple' => false,
				'allowClear' => true,
				'placeholder' => '',
				'tags' => true
			]
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'comment')->textarea() ?>
	</div>
</div>

