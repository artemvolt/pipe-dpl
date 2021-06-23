<?php
declare(strict_types = 1);

/**
 * Шаблон страницы регистрации
 * @var View $this
 * @var ActiveForm $form
 * @var RegistrationForm $model
 */

use app\models\site\RegistrationForm;
use yii\web\View;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Вход';
?>
<div class="panel">
	<div class="panel-container show">
		<div class="panel-content">
			<?php $form = ActiveForm::begin(); ?>
			<div class="form-group">
				<?= $form->field($model, 'username')->textInput(['placeholder' => 'Себастьян Перейра']) ?>
			</div>
			<div class="form-group">
				<?= $form->field($model, 'login')->textInput(['placeholder' => 'Логин для входа']) ?>
			</div>
			<div class="form-group">
				<?= $form->field($model, 'email')->textInput(['placeholder' => 'esteban@domain', 'type' => 'email']) ?>
			</div>
			<div class="form-group">
				<?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Пожалуйста, введите пароль']) ?>
			</div>
			<div class="form-group">
				<?= $form->field($model, 'passwordRepeat')->passwordInput(['placeholder' => 'Пароль ещё раз']) ?>
			</div>
			<?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary btn-lg btn-block', 'name' => 'login-button']) ?>
			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>