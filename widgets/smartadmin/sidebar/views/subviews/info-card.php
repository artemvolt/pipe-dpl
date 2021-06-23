<?php
declare(strict_types = 1);

use app\controllers\UsersController;
use app\models\sys\users\Users;
use yii\bootstrap4\Html;
use yii\web\JsExpression;

?>
<div class="info-card">
	<?= Html::img(UsersController::to('logo-get'), ['class' => 'profile-image rounded-circle user-logo']) ?>
	<div class="info-card-text">
		<a href="<?= UsersController::to('profile') ?>" class="d-flex align-items-center text-white">
			<span class="d-inline-block"><?= Users::Current()->username ?></span>
		</a>
	</div>
	<img src="/img/theme/card-backgrounds/cover-6-lg.png" class="cover" alt="cover">
	<?= Html::a('<i class="fal fa-angle-down"></i>', '#', [
		'class' => "pull-trigger-btn",
		'data-action' => "toggle",
		'data-target' => ".page-sidebar",
		'data-class' => "list-filter-active",
		'data-focus' => "nav_filter_input",
		'onclick' => new JsExpression("return false;")
	]) ?>
</div>