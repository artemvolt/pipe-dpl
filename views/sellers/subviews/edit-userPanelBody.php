<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Sellers $model
 * @var ActiveForm $form
 */

use app\controllers\UsersController;
use app\models\seller\Sellers;
use app\models\sys\users\Users;
use app\widgets\selectmodelwidget\SelectModelWidget;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\View;

?>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'relatedUser')->widget(SelectModelWidget::class, [
			'multiple' => false,
			'data' => ArrayHelper::map([$model->relatedUser], 'id', 'username'),
			'mapAttribute' => 'username',
			'loadingMode' => SelectModelWidget::DATA_MODE_AJAX,
			'selectModelClass' => Users::class,
			'options' => ['placeholder' => ''],
			'ajaxSearchUrl' => UsersController::to('ajax-search', ['column' => 'username'])
		]) ?>
	</div>
</div>