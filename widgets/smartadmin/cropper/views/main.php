<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var CropperWidget $widget
 */

use app\controllers\UsersController;
use app\widgets\smartadmin\cropper\CropperWidget;
use yii\bootstrap4\Html;
use yii\web\View;

?>

<div class="row">
	<div class="col-12">
		<div class="img-container">
			<?= Html::img(UsersController::to('logo-get'), ['id' => $widget->imageId, ['class' => 'user-logo']]) ?>
		</div>
	</div>
</div>

<div class="row mt-3">
	<div class="col-6">
		<label class="btn btn-primary btn-upload btn-block" for="<?= $widget->cropperUploadInputId ?>">
			<?= Html::fileInput('cropper__upload-input', null, [
				'id' => $widget->cropperUploadInputId,
				'class' => 'sr-only',
				'accept' => 'image/*'
			]) ?>
			<span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="Загрузка изображения">
				Загрузить фото
			</span>
		</label>
	</div>
	<div class="col-6">
		<button id="<?= $widget->cropperCropElementId ?>" type="button" class="btn btn-success btn-block">
			<span class="docs-tooltip" data-toggle="tooltip" title="">
				Установить фото профиля
			</span>
		</button>
	</div>
</div>

