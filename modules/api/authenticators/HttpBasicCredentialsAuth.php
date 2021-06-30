<?php
declare(strict_types = 1);

namespace app\modules\api\authenticators;

use app\models\sys\users\Users;
use yii\filters\auth\HttpBasicAuth;
use yii\web\IdentityInterface;

/**
 * Class HttpBasicCredentialsAuth
 * @package app\modules\api\authenticators
 */
class HttpBasicCredentialsAuth extends HttpBasicAuth {
	/**
	 * {@inheritdoc}
	 */
	public function init():void {
		parent::init();

		$this->auth = static function(?string $username, ?string $password):?IdentityInterface {
			$user = Users::findByLogin($username);
			if (null !== $user && $user->validatePassword($password)) {
				return $user;
			}

			return null;
		};
	}
}