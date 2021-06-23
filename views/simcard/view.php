<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var SimCard $model
 */

use app\models\products\SimCard;
use yii\web\View;
use yii\widgets\DetailView;

?>

<?= DetailView::widget([
	'model' => $model
]) ?>
