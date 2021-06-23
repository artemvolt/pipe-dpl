<?php
declare(strict_types = 1);

namespace app\controllers\rest;

use app\models\rest\Users;
use simialbi\yii2\rest\RestDataProvider;
use yii\base\InvalidConfigException;
use yii\web\Controller;

/**
 * Class UsersController
 */
class UsersController extends Controller {

	/**
	 * @return string
	 * @throws InvalidConfigException
	 */
	public function actionIndex():string {

		$dataProvider = new RestDataProvider(['query' => Users::find()]);

		return $this->render('index', compact('dataProvider'));
	}

}