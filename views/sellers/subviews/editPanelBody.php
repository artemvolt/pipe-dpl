<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Sellers $model
 * @var ActiveForm $form
 */

use app\controllers\DealersController;
use app\controllers\StoresController;
use app\models\core\prototypes\ProjectConstants;
use app\models\dealers\Dealers;
use app\models\seller\Sellers;
use app\models\store\Stores;
use app\widgets\selectmodelwidget\SelectModelWidget;
use kartik\form\ActiveForm;
use kartik\date\DatePicker;
use kartik\switchinput\SwitchInput;
use pozitronik\filestorage\widgets\file_input\FileInputWidget;
use yii\helpers\ArrayHelper;
use yii\web\View;

?>
<?php if (!$model->isNewRecord): ?>
	<div class="row">
		<div class="col-md-12">
			<?= $form->field($model, 'currentStatusId')->dropDownList(
				ArrayHelper::map($model->getAvailableStatuses(), 'id', 'name'),
				['prompt' => '']
			) ?>
		</div>
	</div>
<?php endif; ?>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'gender')->dropDownList(ProjectConstants::GENDER, ['prompt' => '']) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'name')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'surname')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'patronymic')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'birthday')->widget(DatePicker::class, [
			'type' => DatePicker::TYPE_COMPONENT_APPEND,
			'pluginOptions' => [
				'autoclose' => true,
				'format' => 'yyyy-mm-dd'
			]
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model->relatedUser??$model, 'login')
			->textInput(['readonly' => !$model->isNewRecord, 'placeholder' => '9123456789']) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model->relatedUser??$model, 'email')->textInput(['readonly' => !$model->isNewRecord]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'passport_series')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'passport_number')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'passport_whom')->textarea(['row' => 6]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'passport_when')->widget(DatePicker::class, [
			'type' => DatePicker::TYPE_COMPONENT_APPEND,
			'pluginOptions' => [
				'autoclose' => true,
				'format' => 'yyyy-mm-dd'
			]
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'reg_address')->textarea(['row' => 6]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'is_resident')->widget(SwitchInput::class, [
			'tristate' => false,
			'pluginOptions' => [
				'size' => 'mini',
				'onText' => '<i class="fa fa-check"></i>',
				'offText' => null
			],
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'non_resident_type')->dropDownList(ProjectConstants::NON_RESIDENT_TYPE, ['prompt' => '']) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'entry_date')->widget(DatePicker::class, [
			'type' => DatePicker::TYPE_COMPONENT_APPEND,
			'pluginOptions' => [
				'autoclose' => true,
				'format' => 'yyyy-mm-dd'
			]
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'keyword')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'is_wireman_shpd')->widget(SwitchInput::class, [
			'tristate' => false,
			'pluginOptions' => [
				'size' => 'mini',
				'onText' => '<i class="fa fa-check"></i>',
				'offText' => null
			],
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
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'dealers')->widget(SelectModelWidget::class, [
			'loadingMode' => SelectModelWidget::DATA_MODE_AJAX,
			'selectModelClass' => Dealers::class,
			'options' => ['placeholder' => ''],
			'ajaxSearchUrl' => DealersController::to('ajax-search')
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'inn')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'snils')->textInput(['placeholder' => '000-000-000 00']) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'passportTranslation')->widget(FileInputWidget::class, [
			'allowVersions' => false
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'migrationCard')->widget(FileInputWidget::class, [
			'allowVersions' => false
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'placeOfStay')->widget(FileInputWidget::class, [
			'allowVersions' => false
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'patent')->widget(FileInputWidget::class, [
			'allowVersions' => false
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'residence')->widget(FileInputWidget::class, [
			'allowVersions' => false
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'temporaryResidence')->widget(FileInputWidget::class, [
			'allowVersions' => false
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'visa')->widget(FileInputWidget::class, [
			'allowVersions' => false
		]) ?>
	</div>
</div>