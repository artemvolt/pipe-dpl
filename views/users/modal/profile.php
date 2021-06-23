<?php
declare(strict_types = 1);

/**
 * @var Users $model
 * @var View $this
 */

use app\models\sys\users\Users;
use yii\bootstrap4\Modal;
use yii\web\View;

?>

<?php Modal::begin([
	'id' => "{$model->formName()}-modal-profile-{$model->id}",
	'size' => Modal::SIZE_LARGE,
	'options' => [
		'class' => 'modal-dialog-large',
	]
]); ?>
<?= $this->render('../profile', [
	'model' => $model
]) ?>

<?php Modal::end(); ?>