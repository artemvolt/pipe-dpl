<?php
declare(strict_types = 1);

namespace app\modules\api\tokenizers\grant_types;

use app\models\sys\users\UsersTokens;
use app\modules\api\exceptions\InvalidGrantException;
use yii\web\Request;

/**
 * Class GrantTypeRefresh
 * @package app\modules\api\tokenizers\grant_types
 */
class GrantTypeRefresh implements GrantTypeInterface {
	private ?string $_refreshToken;

	/**
	 * {@inheritdoc}
	 */
	public function loadRequest(Request $request):void {
		$this->_refreshToken = $request->post('refresh_token');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRefreshToken():?string {
		return $this->_refreshToken;
	}

	/**
	 * {@inheritdoc}
	 * @throws InvalidGrantException
	 */
	public function validate(UsersTokens $authToken, ?UsersTokens $refreshToken):void {
		if (null === $refreshToken || $refreshToken->isNewRecord) {
			$statusIsOk = false;
		} else {
			$statusIsOk = $refreshToken->auth_token === $this->getRefreshToken() && $refreshToken->isValid();
		}
		if (!$statusIsOk) {
			throw new InvalidGrantException();
		}
	}
}