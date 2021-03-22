<?php
declare(strict_types = 1);

namespace app\controllers;

use pozitronik\core\models\DbMonitor;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class DbController
 */
class DbController extends Controller {

	/**
	 * Список процессов на базе данных
	 * @return string
	 */
	public function actionProcessList() {
		$provider = new ArrayDataProvider([
			'allModels' => DbMonitor::ProcessList(),
			'sort' => [
				'attributes' => ['id', 'user_id', 'time'],
				'defaultOrder' => ['time' => SORT_DESC]
			]
		]);
		return $this->render('process-list', [
			'dataProvider' => $provider,
			'message' => Yii::$app->session->getFlash('DbMonitorMessage', false, true)
		]);
	}

	/**
	 * @param int $process_id
	 * @return Response
	 */
	public function actionKill(int $process_id):Response {
		Yii::$app->session->setFlash('DbMonitorMessage', (null === $affected = DbMonitor::kill($process_id))?"None killed":"{$affected} row(s) $affected");
		return $this->redirect(['process-list']);
	}
}
