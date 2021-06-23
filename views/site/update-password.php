<?php
declare(strict_types = 1);

/**
 * Шаблон страницы смены пароля
 * @var View $this
 * @var ActiveForm $form
 * @var UpdatePasswordForm $model
 */

use app\models\site\UpdatePasswordForm;
use yii\web\View;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Смена пароля';
?>
<div class="row">
	<div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-auto">
		<div class="card p-4 rounded-plus bg-faded">
			<div class="panel-tag">
				Пароль пользователя <b><?= $model->user->login ?></b> просрочен и должен быть изменён.
			</div>
			<?php $form = ActiveForm::begin(); ?>

			<?= $form->field($model, 'oldPassword')->passwordInput(['placeholder' => 'Текущий пароль']) ?>
			<?= $form->field($model, 'newPassword')->passwordInput(['placeholder' => 'Новый пароль']) ?>
			<?= $form->field($model, 'newPasswordRepeat')->passwordInput(['placeholder' => 'Новый пароль ещё раз']) ?>

			<?= Html::submitButton('Сменить пароль', ['class' => 'btn btn-primary btn-lg btn-block', 'name' => 'login-button']) ?>

			<?php ActiveForm::end(); ?>

			<div class="text-right mt-1">
				<?= Html::a('Назад', Yii::$app->homeUrl, ['class' => 'btn-link']) ?>
			</div>
		</div>
	</div>
</div>