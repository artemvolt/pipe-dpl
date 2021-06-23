<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Sellers $model
 */
use app\models\seller\Sellers;
use yii\bootstrap4\Modal;
use yii\web\View;
use yii\bootstrap4\ActiveForm;

$modelName = $model->formName();
?>
<?php
Modal::begin([
	'id' => "{$modelName}-modal-edit-user-{$model->id}",
	'size' => Modal::SIZE_LARGE,
	'title' => 'Учётная запись',
	'footer' => $this->render('../subviews/editPanelFooter', [
		'model' => $model,
		'form' => "{$modelName}-modal-edit-user"
	]),//post button outside the form
	'clientOptions' => [
		'backdrop' => true
	],
	'options' => [
		'class' => 'modal-dialog-large',
	]
]); ?>
<?php $form = ActiveForm::begin(['id' => "{$modelName}-modal-edit-user", 'enableAjaxValidation' => true]) ?>
<?= $this->render('../subviews/edit-userPanelBody', compact('model', 'form')) ?>
<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>