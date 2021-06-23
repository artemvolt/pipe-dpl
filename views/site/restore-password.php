<?php
declare(strict_types = 1);

/**
 * Шаблон страницы восстановления пароля
 * @var View $this
 * @var ActiveForm $form
 * @var RestorePasswordForm $model
 */

use app\models\site\RestorePasswordForm;
use yii\web\View;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Восстановление пароля';
?>

<div class="row">
	<div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-auto">
		<div class="card p-4 rounded-plus bg-faded">
			<?php $form = ActiveForm::begin(); ?>

			<?= $form->field($model, 'email')->passwordInput() ?>

			<?= Html::submitButton('Восстановить пароль', ['class' => 'btn btn-primary btn-lg btn-block', 'name' => 'login-button']) ?>

			<?php ActiveForm::end(); ?>

			<div class="text-right mt-1">
				<?= Html::a('Назад', Yii::$app->homeUrl, ['class' => 'btn-link']) ?>
			</div>
		</div>
	</div>
</div>