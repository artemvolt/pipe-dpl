<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Users $model
 * @var ActiveForm $form
 */

use app\models\sys\users\Users;
use kartik\form\ActiveForm;
use yii\web\View;

?>

<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'password')->textInput(['value' => '']) ?>
	</div>
	<div class="col-md-12">
		<?= $form->field($model, 'is_pwd_outdated')->checkbox() ?>
	</div>
</div>
