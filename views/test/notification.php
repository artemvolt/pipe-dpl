<?php
declare(strict_types = 1);
/**
 * @var View $this
 * @var Notifications[] $notifications
 */

use app\modules\notifications\models\Notifications;
use app\modules\notifications\widgets\notification_toast\NotificationToastWidget;
use yii\web\View;

?>


<?php foreach ($notifications as $notification): ?>
	<?= NotificationToastWidget::widget([
		'notification' => $notification,
		'options' => [
			'class' => 'fade show',
		],
	]) ?>
<?php endforeach; ?>