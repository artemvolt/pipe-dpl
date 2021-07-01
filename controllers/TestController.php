<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\site\sse\MessageEventHandler;
use app\models\sys\permissions\filters\PermissionFilter;
use app\models\sys\users\Users;
use app\modules\dol\models\DolAPI;
use app\modules\notifications\models\Notifications;
use pozitronik\dbmon\models\SqlDebugInfo;
use pozitronik\traits\traits\ControllerTrait;
use pozitronik\helpers\Utils;
use ReflectionException;
use Yii;
use yii\base\BaseObject;
use yii\base\UnknownClassException;
use yii\web\Controller;

/**
 * Class TestController
 * Контроллер на потестировать какие-то штуки.
 */
class TestController extends Controller {
	use ControllerTrait;

	/**
	 * @inheritDoc
	 */
	public function behaviors():array {
		return [
			'access' => [
				'class' => PermissionFilter::class
			]
		];

	}

	/**
	 * @return string
	 * @throws ReflectionException
	 * @throws UnknownClassException
	 */
	public function actionIndex():string {
		$actions = self::GetControllerActions();
		return $this->render('index', [
			'actions' => $actions
		]);
	}

	/**
	 * Тестирование добавления отладочной информации в запросы
	 */
	public function actionSqlDebug():void {
		$query = Users::find();

		SqlDebugInfo::addDebugInfo($query, 'debug-process');

		Utils::log($query->createCommand()->rawSql);
		Utils::log($query->all());
	}

	/**
	 *
	 */
	public function actionSse():void {
		/** @noinspection PhpUndefinedFieldInspection */
		$sse = Yii::$app->sse;
		$sse->addEventListener('message', new MessageEventHandler());
		$sse->start();
	}

	/**
	 * @return string
	 */
	public function actionSseClient():string {
		return $this->render('sse-client');
	}

	/**
	 * Тесты доступов
	 */
	public function actionPermissions():void {
		Utils::log(Users::Current()->allPermissions());
	}

	/**
	 * Проверка доступа
	 * @return string
	 */
	public function actionPermissionTest():string {
		return $this->render('permission-test');
	}

	/**
	 * @return string
	 */
	public function actionBadges():string {
		return $this->render('badges');
	}

	public function actionTestNotification():string {
		Notifications::message('Вы получили уведомление');
		return $this->render('notification', [
			'notifications' => Notifications::UserNotifications()
		]);
	}

	public function actionTestNotificationAlert():string {
		Notifications::message('Вы получили уведомление');
		return $this->render('alert', [
			'notifications' => Notifications::UserNotifications()
		]);
	}

	public function actionDolApiSSL() {
		$dolAPI = new DolAPI();
		$dolAPI->smsLogon('1234567890');
	}
}