<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Dealers $model
 * @var ActiveForm $form
 */

use app\controllers\ManagersController;
use app\controllers\SellersController;
use app\controllers\StoresController;
use app\models\branches\active_record\references\RefBranches;
use app\models\dealers\active_record\references\RefDealersGroups;
use app\models\dealers\active_record\references\RefDealersTypes;
use app\models\managers\Managers;
use app\models\seller\Sellers;
use app\models\store\Stores;
use app\widgets\selectmodelwidget\SelectModelWidget;
use kartik\form\ActiveForm;
use app\models\dealers\Dealers;
use yii\web\View;

?>

<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'name')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'code')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'client_code')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'type')->widget(SelectModelWidget::class, [
			'selectModelClass' => RefDealersTypes::class,
			'multiple' => false,
			'options' => [
				'placeholder' => ''
			]
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'group')->widget(SelectModelWidget::class, [
			'selectModelClass' => RefDealersGroups::class,
			'multiple' => false
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'branch')->widget(SelectModelWidget::class, [
			'selectModelClass' => RefBranches::class,
			'multiple' => false
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'sellers')->widget(SelectModelWidget::class, [
			'loadingMode' => SelectModelWidget::DATA_MODE_AJAX,
			'mapAttribute' => 'fio',
			'searchAttribute' => 'surname',
			'concatFields' => 'surname, name, patronymic',
			'selectModelClass' => Sellers::class,
			'options' => ['placeholder' => ''],
			'ajaxSearchUrl' => SellersController::to('ajax-search')
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'managers')->widget(SelectModelWidget::class, [
			'loadingMode' => SelectModelWidget::DATA_MODE_AJAX,
			'mapAttribute' => 'fio',
			'searchAttribute' => 'surname',
			'concatFields' => 'surname, name, patronymic',
			'selectModelClass' => Managers::class,
			'options' => ['placeholder' => ''],
			'ajaxSearchUrl' => ManagersController::to('ajax-search')
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'stores')->widget(SelectModelWidget::class, [
			'loadingMode' => SelectModelWidget::DATA_MODE_AJAX,
			'selectModelClass' => Stores::class,
			'options' => ['placeholder' => ''],
			'ajaxSearchUrl' => StoresController::to('ajax-search')
		]) ?>
	</div>
</div>
