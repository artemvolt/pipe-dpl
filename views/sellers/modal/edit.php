<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Sellers $model
 */
use app\models\seller\Sellers;
use pozitronik\widgets\BadgeWidget;
use yii\bootstrap4\Modal;
use yii\web\View;
use yii\bootstrap4\ActiveForm;

$modelName = $model->formName();
?>
<?php
Modal::begin([
	'id' => "{$modelName}-modal-edit-{$model->id}",
	'size' => Modal::SIZE_LARGE,
	'title' => BadgeWidget::widget([
		'items' => $model,
		'subItem' => 'name'
	]),
	'footer' => $this->render('../subviews/editPanelFooter', [
		'model' => $model,
		'form' => "{$modelName}-modal-edit"
	]),//post button outside the form
	'clientOptions' => [
		'backdrop' => true
	],
	'options' => [
		'class' => 'modal-dialog-large',
	]
]); ?>
<?php $form = ActiveForm::begin(['id' => "{$modelName}-modal-edit", 'enableAjaxValidation' => true]) ?>
<?= $this->render('../subviews/editPanelBody', compact('model', 'form')) ?>
<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>