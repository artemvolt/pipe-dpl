<?php
declare(strict_types = 1);

namespace app\modules\notifications\widgets\notification_toast;

use app\modules\notifications\models\Notifications;
use yii\bootstrap4\Toast;

/**
 * Class NotificationToastWidget
 * @property Notifications $notification
 */
class NotificationToastWidget extends Toast {

	public Notifications $notification;

	/**
	 * @inheritDoc
	 */
	public function init():void {
		$this->dateTime = $this->notification->timestamp;
		$this->body = $this->notification->message;
		$this->options = [
			'class' => 'fade show'
		];
		parent::init();
		$this->getView()->registerJs("$('#{$this->id}').show()");
		$this->notification->delete();
	}

}