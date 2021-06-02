<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\core\prototypes\DefaultController;
use app\models\sys\users\Users;
use app\models\sys\users\UsersSearch;
use DomainException;
use pozitronik\core\traits\ControllerTrait;
use pozitronik\sys_exceptions\models\LoggedException;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class UsersController
 */
class UsersController extends DefaultController {

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
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'login-as-another-user'  => ['POST'],
				],
			],
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
			throw new LoggedException(new NotFoundHttpException());
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
	 * @throws LoggedException
	 * @throws Throwable
	 * @throws Exception
	 */
	public function actionUpdatePassword(int $id) {
		if (null === $user = Users::findOne($id)) {
			throw new LoggedException(new NotFoundHttpException());
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
	 * @throws LoggedException
	 * @throws Throwable
	 */
	public function actionLogoUpload():array {
		try {
			Users::Current()->uploadAttribute('avatar');
		} catch (Throwable $t) {
			throw new LoggedException($t);
		}

		return [];
	}

	/**
	 * @param int|null $id
	 * @throws LoggedException
	 */
	public function actionLogoGet(?int $id = null):void {
		$user = null === $id?Users::Current():Users::findOne($id);
		if (null === $user) {
			throw new LoggedException(new NotFoundHttpException());
		}
		if (null === $user->fileAvatar) {
			Yii::$app->response->sendFile(Yii::getAlias(Users::DEFAULT_AVATAR_ALIAS_PATH));
		} else {
			$user->fileAvatar->download();
		}
	}
	/**
	 * Авторизоваться под другим пользователем
	 *
	 * @return Response
	 */
	public function actionLoginAsAnotherUser() {
		try {
			$userId = (int) Yii::$app->request->post('userId');
			Yii::$app->user->loginAsAnotherUser($userId);
			Yii::$app->session->setFlash('success', 'Вы успешно авторизовались');
			return $this->redirect(['profile', 'id' => $userId]);
		} catch (DomainException $e) {
			Yii::$app->session->setFlash('error', 'Ошибка доступа');
			return $this->redirect(Url::toRoute(['users/index']));
		}
	}

	/**
	 * Вернуться в свою учетную запись
	 *
	 * @return Response
	 */
	public function actionLoginBack()
	{
		try {
			$originalId = Yii::$app->user->getOriginalUserId();
			Yii::$app->user->loginBackToOriginUser();
			Yii::$app->session->setFlash('success', 'Вы успешно вернулись обратно');
			return $this->redirect(['profile', 'id' => $originalId]);
		} catch (DomainException $e) {
			Yii::$app->session->setFlash('error', 'Ошибка доступа');
			return $this->redirect(Url::toRoute(['users/index']));
		}
	}

}
