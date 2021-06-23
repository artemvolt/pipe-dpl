<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var SimCard $model
 * @var ActiveForm|string $form
 */

use kartik\form\ActiveForm;
use app\models\products\SimCard;
use yii\bootstrap4\Html;
use yii\web\View;

?>

<?= Html::submitButton('Сохранить', [
		'class' => $model->isNewRecord?'btn btn-success float-right':'btn btn-primary float-right',
		'form' => is_object($form)?$form->id:$form
	]
) ?>
