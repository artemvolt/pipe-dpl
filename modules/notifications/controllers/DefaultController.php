<?php
declare(strict_types = 1);

namespace app\modules\notifications\controllers;

use app\models\sys\permissions\filters\PermissionFilter;
use app\modules\notifications\models\Notifications;
use Throwable;
use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class DefaultController
 */
class DefaultController extends Controller {

	/**
	 * @inheritDoc
	 */
	public function behaviors():array {
		return [
			[
				'class' => ContentNegotiator::class,
				'formats' => [
					'application/json' => Response::FORMAT_JSON
				]
			],
			'access' => [
				'class' => PermissionFilter::class
			]
		];
	}

	/**
	 * Acknowledge user notification
	 * @return string[]
	 * @throws Throwable
	 */
	public function actionAcknowledge():array {
		if (null !== $notification = Notifications::findOne(Yii::$app->request->post('id'))) {
			Notifications::Acknowledge($notification->object_id);
			return ['output' => 'ok', 'message' => ''];
		}
		return [];
	}
}