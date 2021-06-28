<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\sys\permissions\filters\PermissionFilter;
use pozitronik\traits\traits\ControllerTrait;
use yii\web\Controller;

/**
 * Class ServiceController
 */
class ServiceController extends Controller {
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
	 */
	public function actionIndex():string {
		return $this->render('index');
	}

}
