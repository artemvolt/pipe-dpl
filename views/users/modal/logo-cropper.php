<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Users $user
 */

use app\models\sys\users\Users;
use app\widgets\smartadmin\cropper\CropperWidget;
use yii\bootstrap4\Modal;
use yii\web\View;

?>

<?php Modal::begin(['id' => 'cropperModal', 'title' => 'Фото профиля']) ?>

<?= CropperWidget::widget([
	'modalId' => 'cropperModal',
	'model' => $user,
	'attribute' => 'avatar',
	'pluginOptions' => [
		'aspectRatio' => 1,
		'viewMode' => 2,
		'zoomable' => false,
		'restore' => false,
		'checkCrossOrigin' => false,
		'checkOrientation' => false,
		'highlight' => false,
		'movable' => false,
		'rotatable' => false,
		'scalable' => false,
		'toggleDragModeOnDblclick' => false,
		'guides' => false
	]
]) ?>

<?php Modal::end();
