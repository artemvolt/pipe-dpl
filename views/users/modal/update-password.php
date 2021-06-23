<?php
declare(strict_types = 1);

/**
 * @var Users $model
 * @var View $this
 */

use app\models\sys\users\Users;
use yii\bootstrap4\Modal;
use yii\web\View;
use yii\bootstrap4\ActiveForm;

?>

<?php Modal::begin([
	'id' => "{$model->formName()}-modal-update-password-{$model->id}",
	'size' => Modal::SIZE_LARGE,
	'footer' => $this->render('../subviews/editPanelFooter', [
		'model' => $model,
		'form' => "{$model->formName()}-modal-update-password"
	]),
	'options' => [
		'class' => 'modal-dialog-large',
	]
]); ?>
<?php $form = ActiveForm::begin(['id' => "{$model->formName()}-modal-update-password"]); ?>
<?= $this->render('../subviews/update-passwordPanelBody', compact('model', 'form')) ?>
<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>