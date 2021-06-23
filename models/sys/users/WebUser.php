<?php
declare(strict_types = 1);

namespace app\models\sys\users;

use DomainException;
use Throwable;
use Yii;
use yii\web\Cookie;
use yii\web\User;

/**
 * Class WebUser
 * @package app\models\vendor\yii2\web
 *
 * @property Users $identity
 */
class WebUser extends User {

	/**
	 * Вернуть id оригинального пользователя,
	 * из под которого авторизовались под другим пользователем
	 * @return null|int null, если текущий пользователь и есть оригинальный
	 */
	public function getOriginalUserId():?int {
		return $this->isLoginAsAnotherUser()?(int)Yii::$app->request->cookies->get('fear')->value:null;

	}

	/**
	 * @return bool
	 */
	public function isLoginAsAnotherUser():bool {
		return Yii::$app->request->cookies->has('fear');
	}

	/**
	 * Вернуться в родную учетную запись
	 * @return void
	 */
	public function loginBackToOriginUser():void {
		$webUser = Yii::$app->user;
		if ($webUser->isGuest) {
			throw new DomainException("Вы не авторизованы");
		}

		if (null === $existentUser = Users::findIdentity($id = $this->getOriginalUserId())) {
			throw new DomainException("Пользователь с id $id не найден");
		}

		$webUser->login($existentUser);
		Yii::$app->response->cookies->remove('fear');
	}

	/**
	 * Авторизоваться под другим пользователем
	 *
	 * @param int $userId
	 * @throws Throwable
	 */
	public function loginAsAnotherUser(int $userId):void {
		$webUser = Yii::$app->user;
		if ($webUser->isGuest) {
			throw new DomainException("Вы не авторизованы");
		}

		if (!$webUser->identity->hasPermission(['login_as_another_user'])) {
			throw new DomainException("Вы не можете авторизоваться под другим пользователем");
		}

		if (null === $existentUser = Users::findIdentity($userId)) {
			throw new DomainException("Пользователь с id $userId не найден");
		}

		$originalUserId = $webUser->identity->id;

		$webUser->login($existentUser);

		Yii::$app->response->cookies->add(
			new Cookie([
				'name' => 'fear',
				'value' => $originalUserId,
			])
		);
	}

	/**
	 * @param bool $destroySession
	 * @return bool
	 */
	public function logout($destroySession = true):bool {
		$isLogout = parent::logout($destroySession);
		if ($isLogout) {
			Yii::$app->response->cookies->remove('fear');
		}
		return $isLogout;
	}
}
