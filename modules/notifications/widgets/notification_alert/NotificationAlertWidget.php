<?php
declare(strict_types = 1);

namespace app\modules\notifications\widgets\notification_alert;

use app\modules\notifications\models\Notifications;
use app\modules\notifications\NotificationsModule;
use app\modules\notifications\NotificationsModuleAssets;
use Exception;
use Throwable;
use yii\base\InvalidConfigException;
use yii\bootstrap4\Alert;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * Class NotificationAlertWidget
 * @property Notifications $notification
 * @property int $type См. константы
 * @property null|false|string $icon
 * @property null|false|string $acknowledgeButton
 * @property null|false|string $dismissButton
 */
class NotificationAlertWidget extends Alert {

	public const TYPE_PRIMARY = 0;
	public const TYPE_SUCCESS = 1;
	public const TYPE_DANGER = 2;
	public const TYPE_WARNING = 3;
	public const TYPE_INFO = 4;
	public const TYPE_SECONDARY = 5;

	public const TYPE_CLASSES = [
		self::TYPE_PRIMARY => 'alert-primary border-primary text-primary',
		self::TYPE_SUCCESS => 'alert-success border-success text-success',
		self::TYPE_DANGER => 'alert-danger border-danger text-danger',
		self::TYPE_WARNING => 'alert-warning border-warning text-warning',
		self::TYPE_INFO => 'alert-info border-info text-info',
		self::TYPE_SECONDARY => 'alert-secondary border-secondary text-secondary'
	];
	public const TYPE_ICONS = [
		self::TYPE_PRIMARY => '',
		self::TYPE_SUCCESS => '<div class="alert-icon"><span class="icon-stack icon-stack-md"><i class="base-7 icon-stack-3x color-success-600"></i><i class="fal fa-check icon-stack-1x text-white"></i></span></div>',
		self::TYPE_DANGER => '<div class="alert-icon"><span class="icon-stack icon-stack-md"><i class="base-7 icon-stack-3x color-danger-900"></i><i class="fal fa-times icon-stack-1x text-white"></i></span></div>',
		self::TYPE_WARNING => '<div class="alert-icon"><i class="fal fa-exclamation-triangle"></i></div>',
		self::TYPE_INFO => '<div class="alert-icon"><i class="fal fa-info-circle"></i></div>',
		self::TYPE_SECONDARY => ''
	];

	public Notifications $notification;
	public int $type = self::TYPE_PRIMARY;
	public $icon;
	public $acknowledgeButton;
	public $dismissButton;

	public $closeButton = false;

	/**
	 * @inheritDoc
	 */
	public function init():void {
		$this->body = $this->getBody();
		NotificationsModuleAssets::register($this->view);
		$this->options = [
			'class' => ArrayHelper::getValue(self::TYPE_CLASSES, $this->type, '')
		];
		parent::init();
		$this->view->registerJs("$('#{$this->id}').show()");
	}

	private function getBody():string {
		return Html::tag('div',
			$this->getIcon().
			$this->getMessage().
			$this->getAcknowledgeButton().
			$this->getDismissButton(), [
				"class" => "d-flex align-items-center"
			]);
	}

	/**
	 * @return string
	 */
	private function getMessage():string {
		return Html::tag('div', Html::tag('span', $this->notification->message, [
			'class' => 'h5 m-0 fw-700'
		]), [
			'class' => 'flex-1'
		]);
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	private function getIcon():string {
		if (false === $this->icon) return '';
		return $this->icon??ArrayHelper::getValue(self::TYPE_ICONS, $this->type, '');
	}

	/**
	 * @return string
	 */
	private function getDismissButton():string {
		if (false === $this->dismissButton) return '';
		return $this->dismissButton??Html::button("Отложить", [
				'class' => "btn btn-danger btn-pills btn-sm btn-w-m waves-effect waves-themed",
				'data-dismiss' => "alert",
				'aria-label' => "Отложить"
			]);
	}

	/**
	 * @return string
	 * @throws Throwable
	 * @throws InvalidConfigException
	 */
	private function getAcknowledgeButton():string {
		if (false === $this->acknowledgeButton) return '';
		return $this->acknowledgeButton??Html::a("Прочитано", '#', [
				'class' => "btn btn-info btn-pills btn-sm btn-w-m  mr-1 waves-effect waves-themed",
				'data-dismiss' => "alert",
				'aria-label' => "Прочитано",
				'onclick' => new JsExpression("post('".NotificationsModule::to(['default/acknowledge'])."', ".Json::encode(['id' => $this->notification->id]).")")
			]);
	}
}