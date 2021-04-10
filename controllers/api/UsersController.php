<?php
declare(strict_types = 1);

namespace app\controllers\api;

use app\models\sys\permissions\filters\PermissionFilter;
use app\models\sys\users\Users;
use yii\filters\auth\CompositeAuth;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * Class UserController
 */
class UsersController extends ActiveController {
	public $modelClass = Users::class;

	/**
	 * @inheritDoc
	 */
	public function behaviors():array {
		return [
			'contentNegotiator' => [
				'class' => ContentNegotiator::class,
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
					'application/xml' => Response::FORMAT_XML,
				],
			],
			'verbFilter' => [
				'class' => VerbFilter::class,
				'actions' => $this->verbs(),
			],
			'authenticator' => [
				'class' => CompositeAuth::class,
			],
			'rateLimiter' => [
				'class' => RateLimiter::class,
			],
			'access' => [
				'class' => PermissionFilter::class
			]
		];

	}
}