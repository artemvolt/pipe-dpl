<?php
declare(strict_types = 1);

/**
 * Шаблон страницы сброса пароля
 * @var View $this
 * @var ActiveForm $form
 * @var UpdatePasswordForm $model
 * @var string $code Код сброса пароля
 */

use app\controllers\SiteController;
use app\models\site\UpdatePasswordForm;
use yii\web\View;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Смена пароля';
?>
<div class="panel">
	<div class="panel-container show">
		<div class="panel-content">
			<?php $form = ActiveForm::begin([
				'action' => SiteController::to('reset-password', ['code' => $code]),
				'method' => 'post'
			]); ?>
			<div class="form-group">
				<?= $form->field($model, 'newPassword')->passwordInput(['placeholder' => 'Новый пароль']) ?>
			</div>
			<div class="form-group">
				<?= $form->field($model, 'newPasswordRepeat')->passwordInput(['placeholder' => 'Новый пароль ещё раз']) ?>
			</div>
			<?= Html::submitButton('Сменить пароль', ['class' => 'btn btn-primary btn-lg btn-block', 'name' => 'login-button']) ?>
			<?php ActiveForm::end(); ?>
			<?= Html::a('Назад', '/', ['class' => 'btn-link mar-lft']) ?>
		</div>
	</div>
</div>