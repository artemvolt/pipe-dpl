<?php
declare(strict_types = 1);

namespace app\modules\api\controllers;

use app\models\sys\permissions\filters\PermissionFilter;
use cusodede\jwt\JwtHttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller as YiiRestController;
use yii\web\Response;

/**
 * Контроллер для справки, чтобы проверить
 * авторизацию, например
 *
 * Class VersionController
 * @package app\modules\api\controllers
 */
class VersionController extends YiiRestController {
	/**
	 * {@inheritdoc}
	 */
	public function behaviors():array {
		return [
			'contentNegotiator' => [
				'class' => ContentNegotiator::class,
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
			],
			'verbFilter' => [
				'class' => VerbFilter::class,
				'actions' => $this->verbs(),
			],
			'authenticator' => [
				'class' => JwtHttpBearerAuth::class
			],
			'access' => [
				'class' => PermissionFilter::class
			]
		];
	}

	/**
	 * @return string[]
	 */
	public function actionIndex() {
		return [
			'version' => "1.0"
		];
	}
}