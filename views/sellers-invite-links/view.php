<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var SellerInviteLink $model
 */

use app\models\seller\SellerInviteLink;
use yii\web\View;
use yii\widgets\DetailView;

?>

<?= DetailView::widget([
	'model' => $model
]) ?>
