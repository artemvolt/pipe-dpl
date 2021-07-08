<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var PermissionsCollections $model
 * @var ActiveForm $form
 */

use app\controllers\PermissionsCollectionsController;
use app\controllers\PermissionsController;
use app\models\sys\permissions\active_record\Permissions;
use app\models\sys\permissions\active_record\PermissionsCollections;
use cusodede\multiselect\MultiSelectListBox;
use kartik\form\ActiveForm;
use pozitronik\helpers\ArrayHelper;
use yii\bootstrap4\Html;
use yii\web\View;

?>

<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'name')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'comment')->textarea() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label" for="search-permission">Поиск</label>
			<?= Html::input(
				'text',
				'search-permission',
				null,
				['class' => 'form-control', 'id' => 'search-permission']
			) ?>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= ([] === $permissions = Permissions::find()->all())/*Можно назначить только права из БД*/
			?Html::a('Сначала создайте доступы', PermissionsController::to('index'), ['class' => 'btn btn-warning'])
			:$form->field($model, 'relatedPermissions')->widget(MultiSelectListBox::class, [
				'options' => [
					'multiple' => true,
				],
				'data' => ArrayHelper::map($permissions, 'id', 'name'),
			]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= ([] === $permissionsCollections = PermissionsCollections::find()->where(null === $model->id?'1 = 1':['<>', 'id', $model->id])->all())/*Проверяем, есть ли другие коллекции, кроме этой*/
			?Html::a('Сначала создайте другие группы доступов', PermissionsCollectionsController::to('index'), ['class' => 'btn btn-warning'])
			:$form->field($model, 'relatedSlavePermissionsCollections')->widget(MultiSelectListBox::class, [
				'options' => [
					'multiple' => true,
				],
				'data' => ArrayHelper::map($permissionsCollections, 'id', 'name'),
			]) ?>
	</div>
</div>

