<?php
declare(strict_types = 1);

/**
 * @var Users $model
 * @var View $this
 */

use app\controllers\SiteController;
use app\controllers\UsersController;
use app\models\sys\users\Users;
use yii\web\View;
use yii\bootstrap4\Html;

?>

	<div class="row">
		<div class="col-lg-6 col-xl-6 order-lg-1 order-xl-1">
			<div class="card mb-g rounded-top">
				<div class="row no-gutters row-grid">
					<div class="col-4">
						<div class="d-flex flex-column align-items-center justify-content-center p-4">
							<?= Html::img(UsersController::to('logo-get'), [
								'class' => "rounded-circle shadow-2 img-thumbnail user-logo",
								'style' => "width: 160px; height: 160px",
							]) ?>
							<h5 class="mb-0 fw-700 text-center mt-3">
								<?= Users::Current()->username ?>
							</h5>
						</div>
					</div>
					<div class="col-8">
						<div class="d-flex flex-column align-items-start p-4">
							<div class="d-flex flex-column mb-2">
								<?= $this->render('subviews/editable-input', [
									'model' => $model,
									'attribute' => 'username',
									'url' => UsersController::to('editAction', ['id' => $model->id])
								]) ?>
							</div>
							<div class="d-flex flex-column mb-2">
								<?= $this->render('subviews/editable-input', [
									'model' => $model,
									'attribute' => 'login',
									'url' => UsersController::to('editAction', ['id' => $model->id])
								]) ?>
							</div>
							<div class="d-flex flex-column mb-2">
								<?= $this->render('subviews/editable-input', [
									'model' => $model,
									'attribute' => 'email',
									'url' => UsersController::to('editAction', ['id' => $model->id])
								]) ?>
							</div>
						</div>
					</div>
					<div class="col-12">
						<div class="p-3 text-center">
							<?= Html::a("<i class='fal fa-fw fa-image-polaroid'></i> Загрузить фото", "#", [
								"data-toggle" => "modal",
								"data-target" => "#cropperModal",
								"class" => "btn-link font-weight-bold"
							]) ?>
							<span class="text-primary d-inline-block mx-3">●</span>
							<?= Html::a("<i class='fal fa-fw fa-lock'></i> Обновить пароль", SiteController::to('update-password'), [
								"class" => "btn-link font-weight-bold"
							]) ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?= $this->render('modal/logo-cropper', [
	'user' => $model
]) ?>