<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Stores $model
 * @var ActiveForm $form
 */

use app\controllers\DealersController;
use app\controllers\ManagersController;
use app\controllers\SellersController;
use app\models\branches\active_record\references\RefBranches;
use app\models\dealers\Dealers;
use app\models\managers\Managers;
use app\models\regions\active_record\references\RefRegions;
use app\models\seller\Sellers;
use app\models\store\active_record\references\RefSellingChannels;
use app\models\store\active_record\references\RefStoresTypes;
use app\models\store\Stores;
use app\widgets\selectmodelwidget\SelectModelWidget;
use kartik\form\ActiveForm;
use pozitronik\references\widgets\reference_select\ReferenceSelectWidget;
use yii\web\View;

?>

<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'name')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'type')->widget(ReferenceSelectWidget::class, [
			'referenceClass' => RefStoresTypes::class
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'branch')->widget(ReferenceSelectWidget::class, [
			'referenceClass' => RefBranches::class,
			'options' => ['placeholder' => '']
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'region')->widget(ReferenceSelectWidget::class, [
			'referenceClass' => RefRegions::class,
			'options' => ['placeholder' => '']
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'selling_channel')->widget(ReferenceSelectWidget::class, [
			'referenceClass' => RefSellingChannels::class,
			'options' => ['placeholder' => '']
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
		<?= $form->field($model, 'dealer')->widget(SelectModelWidget::class, [
			'loadingMode' => SelectModelWidget::DATA_MODE_AJAX,
			'selectModelClass' => Dealers::class,
			'options' => ['placeholder' => '', 'multiple' => false],
			'ajaxSearchUrl' => DealersController::to('ajax-search')
		]) ?>
	</div>
</div>