<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\core\prototypes\DefaultController;
use app\models\sys\permissions\filters\PermissionFilter;
use app\models\sys\users\Users;
use app\models\sys\users\UsersSearch;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class UsersController
 */
class UsersController extends DefaultController {

	protected const DEFAULT_TITLE = "Пользователи";

	/**
	 * Поисковая модель пользователя
	 * @var string
	 */
	public string $modelSearchClass = UsersSearch::class;

	/**
	 * Модель пользователя
	 * @var string
	 */
	public string $modelClass = Users::class;

	public bool $enablePrototypeMenu = false;

	/**
	 * Переопределим базовую директорию views
	 * @return string
	 */
	public function getViewPath():string {
		return '@app/views/users';
	}

	public function behaviors():array {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => ContentNegotiator::class,
				'only' => ['logo-upload'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
			],
			'access' => [
				'class' => PermissionFilter::class
			]
		]);
	}

	/**
	 * Профиль пользователя
	 * @param null|int $id
	 * @return string|null
	 * @throws Throwable
	 */
	public function actionProfile(?int $id = null):?string {
		$user = (null === $id)?Users::Current():Users::findOne($id);
		if (null === $user) {
			throw new NotFoundHttpException();
		}
		if (Yii::$app->request->isAjax) {
			return $this->renderAjax('modal/profile', [
				'model' => $user
			]);
		}
		return $this->render('profile', [
			'model' => $user
		]);
	}

	/**
	 * @param int $id
	 * @return string|Response
	 * @throws Throwable
	 * @throws Exception
	 */
	public function actionUpdatePassword(int $id) {
		if (null === $user = Users::findOne($id)) {
			throw new NotFoundHttpException();
		}
		if ($user->updateModelFromPost()) {
			return $this->redirect(['profile', 'id' => $user->id]);
		}
		if (Yii::$app->request->isAjax) {
			return $this->renderAjax('modal/update-password', [
				'model' => $user
			]);
		}
		return $this->render('update-password', [
			'model' => $user
		]);
	}

	/**
	 * Загрузка фото профиля
	 * @return array
	 * @throws Throwable
	 */
	public function actionLogoUpload():array {
		Users::Current()->uploadAttribute('avatar');
		return [];
	}

	/**
	 * @param int|null $id
	 * @throws NotFoundHttpException
	 * @throws ForbiddenHttpException
	 */
	public function actionLogoGet(?int $id = null):void {
		$user = null === $id?Users::Current():Users::findOne($id);
		if (null === $user) {
			throw new NotFoundHttpException();
		}
		Yii::$app->response->sendFile(null === $user->fileAvatar
			?Yii::getAlias(Users::DEFAULT_AVATAR_ALIAS_PATH)
			:$user->fileAvatar->path
		);
	}

	/**
	 * Авторизоваться под другим пользователем
	 *
	 * @param int $userId
	 * @return Response
	 * @throws Throwable
	 */
	public function actionLoginAsAnotherUser(int $userId):Response {
		Yii::$app->user->loginAsAnotherUser($userId);
		return $this->redirect(Yii::$app->homeUrl);
	}

	/**
	 * Вернуться в свою учетную запись
	 *
	 * @return Response
	 */
	public function actionLoginBack():Response {
		Yii::$app->user->loginBackToOriginUser();
		return $this->redirect(Yii::$app->homeUrl);
	}

}
