<?php
declare(strict_types = 1);
/**
 * @var View $this
 * @var Notifications[] $notifications
 */

use app\modules\notifications\models\Notifications;
use app\modules\notifications\widgets\notification_alert\NotificationAlertWidget;
use yii\web\View;

?>

<?php foreach ($notifications as $notification): ?>
	<?= NotificationAlertWidget::widget([
		'type' => NotificationAlertWidget::TYPE_SUCCESS,
		'notification' => $notification,
	]) ?>
<?php endforeach; ?>