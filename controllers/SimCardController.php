<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\core\prototypes\DefaultController;
use app\models\products\SimCard;
use app\models\products\SimCardSearch;
use yii\web\NotFoundHttpException;

/**
 * Class SimCardController
 */
class SimCardController extends DefaultController {

	public string $modelClass = SimCard::class;
	public string $modelSearchClass = SimCardSearch::class;

	/**
	 * @inheritDoc
	 */
	public function getViewPath():string {
		return '@app/views/simcard';
	}

	/**
	 * Продажа карты (протипирую)
	 * @param int $id
	 * @throws NotFoundHttpException
	 */
	public function actionSell(int $id):void {
		if (null === $model = SimCard::findOne($id)) throw new NotFoundHttpException();

		$model->doSell();
	}
}