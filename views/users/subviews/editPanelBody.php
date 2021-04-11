<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Users $model
 * @var ActiveForm $form
 */

use app\models\sys\permissions\Permissions;
use app\models\sys\permissions\PermissionsCollections;
use app\models\sys\users\Users;
use dosamigos\multiselect\MultiSelectListBox;
use kartik\form\ActiveForm;
use pozitronik\helpers\ArrayHelper;
use yii\web\View;

$this->registerCss(".ms-container {width:100%}");

?>

<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'username')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'comment')->textarea() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= ([] === $permissionsList = ArrayHelper::map(Permissions::find()->all(), 'id', 'name'))?'Доступы не созданы':$form->field($model, 'relatedPermissions')->widget(MultiSelectListBox::class, [
			'options' => [
				'multiple' => true,
			],
			'data' => $permissionsList,
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= ([] === $permissionsCollectionsList = ArrayHelper::map(PermissionsCollections::find()->all(), 'id', 'name'))?'Группы доступов не созданы':$form->field($model, 'relatedPermissionsCollections')->widget(MultiSelectListBox::class, [
			'options' => [
				'multiple' => true,
			],
			'data' => $permissionsCollectionsList,
		]) ?>
	</div>
</div>

