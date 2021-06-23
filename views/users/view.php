<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Users $model
 */

use app\models\sys\users\Users;
use yii\web\View;
use yii\widgets\DetailView;

?>

<?= DetailView::widget([
	'model' => $model
]) ?>
