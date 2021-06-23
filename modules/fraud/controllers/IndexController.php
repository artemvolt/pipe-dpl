<?php
declare(strict_types = 1);

namespace app\modules\fraud\controllers;

use app\modules\fraud\components\queue\ChangeFraudStepWithRepeatValidateJob;
use app\modules\fraud\models\FraudCheckStepSearch;
use app\modules\notifications\models\Notifications;
use Throwable;
use yii\web\Controller;
use Yii;

/**
 * Class IndexController
 * @package app\modules\fraud\controllers
 */
class IndexController extends Controller {
	/**
	 * @return string
	 * @throws Throwable
	 */
	public function actionList():string {
		$request = Yii::$app->request;

		if (($request->isPost && $repeatValidateId = (int)$request->post('repeat_validate_id')) && $repeatValidateId > 0) {
			Yii::$app->queue->push(new ChangeFraudStepWithRepeatValidateJob([
				'fraudStepId' => $repeatValidateId
			]));
			Notifications::message('Задание на перерасчет фрода успешно добавлено в очередь');
		}

		$params = $request->queryParams;
		$searchModel = new FraudCheckStepSearch();
		return $this->render('list', [
			'searchModel' => $searchModel,
			'dataProvider' => $searchModel->search($params),
			'notifications' => Notifications::UserNotifications()
		]);
	}
}
