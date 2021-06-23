<?php
declare(strict_types = 1);
/**
 * Шаблон страницы ошибки
 * @var View $this
 * @var HttpException $exception
 */

use yii\bootstrap4\Html;
use yii\web\View;
use yii\web\HttpException;

$this->title = 'Ошибка';
?>

<div class="row">
	<div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-auto">
		<div class="card p-4 rounded-plus bg-faded text-center">
			<h1 class="error-code text-primary"><?= Html::encode($exception->statusCode) ?></h1>
			<p><?= nl2br(Html::encode($exception->getMessage())) ?></p>
			<div><i class="fa fa-spinner fa-pulse fa-3x fa-fw text-primary"></i></div>
			<div class="mt-5">
				<?= Html::a('Назад', Yii::$app->homeUrl, ['class' => 'btn-link']) ?>
			</div>
		</div>
	</div>
</div>
