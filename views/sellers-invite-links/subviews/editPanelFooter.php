<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var SellerInviteLink $model
 * @var ActiveForm|string $form
 */

use app\models\seller\SellerInviteLink;
use kartik\form\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

?>

<?= Html::submitButton($model->isNewRecord?'Пригласить':'Редактировать', [
		'class' => $model->isNewRecord?'btn btn-success float-right':'btn btn-primary float-right',
		'form' => is_object($form)?$form->id:$form
	]
) ?>
