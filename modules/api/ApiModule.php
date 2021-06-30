<?php
declare(strict_types = 1);

namespace app\modules\api;

use pozitronik\traits\traits\ModuleTrait;
use Yii;
use yii\base\Module as YiiBaseModule;
use yii\web\Response;
use app\modules\api\error\ErrorHandler as ApiErrorHandler;

/**
 * Class ApiModule
 * @package app\modules\api
 */
class ApiModule extends YiiBaseModule {
	use ModuleTrait;

	/**
	 * {@inheritdoc}
	 */
	public function init():void {
		parent::init();

		//не применимо к REST API
		Yii::$app->request->enableCsrfCookie = false;
		//чтобы без сюрпризов в виде html разметки
		Yii::$app->response->format = Response::FORMAT_JSON;
		//просто как рекомендация
		Yii::$app->user->enableSession = false;

		$errorHandler = Yii::createObject(ApiErrorHandler::class);
		$errorHandler->register();

		Yii::$app->set('errorHandler', $errorHandler);
	}
}