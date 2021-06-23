<?php
declare(strict_types = 1);

namespace app\controllers\api;

use app\models\core\Status;
use app\models\site\LoginForm;
use pozitronik\helpers\ArrayHelper;
use Throwable;
use Yii;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Class LoginController
 */
class LoginController extends Controller {

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
		];

	}

	/**
	 * @inheritDoc
	 */
	protected function verbs():array {
		return [
			'index' => ['POST'],
		];
	}

	/**
	 * @return array
	 * @throws Throwable
	 */
	public function actionIndex():array {
		$model = new LoginForm();
		$headersAuth = [
			'login' => ArrayHelper::getValue(Yii::$app->request->headers, 'login'),
			'password' => ArrayHelper::getValue(Yii::$app->request->headers, 'password')
		];
		/*Допускается авторизация из параметров, переданных в заголовке*/
		if (!($model->load(Yii::$app->request->post()) || $model->load($headersAuth, ''))) {
			return [
				'status' => Status::STATUS_BAD_REQUEST,
				'message' => "Need username and password.",
				'data' => ''
			];
		}
		if (null === $token = $model->getToken(Yii::$app->request->userIP, Yii::$app->request->userAgent)) {
			return [
				'status' => Status::STATUS_UNAUTHORIZED,
				'message' => array_shift($model->errors),
				'data' => ''
			];
		}
		return [
			'status' => Status::STATUS_FOUND,
			'message' => 'Login Succeed, save your token',
			'data' => [
				'id' => $model->user->username,
				'token' => $token->auth_token,
				'valid' => $token->valid
			]
		];
	}
}