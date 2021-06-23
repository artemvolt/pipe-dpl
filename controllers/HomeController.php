<?php
declare(strict_types = 1);

namespace app\controllers;

use pozitronik\core\traits\ControllerTrait;
use yii\web\Controller;

/**
 * Class HomeController
 * @package app\controllers
 */
class HomeController extends Controller {
	use ControllerTrait;

	/**
	 * @return string
	 */
	public function actionHome():string {
		return $this->render('home');
	}

}
