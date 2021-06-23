<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Products $model
 */

use app\models\products\Products;
use yii\web\View;
use yii\widgets\DetailView;

?>

<?= DetailView::widget([
	'model' => $model
]) ?>
