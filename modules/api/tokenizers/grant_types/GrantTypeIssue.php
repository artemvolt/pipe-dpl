<?php
declare(strict_types = 1);

namespace app\modules\api\tokenizers\grant_types;

use app\models\sys\users\UsersTokens;
use app\modules\api\exceptions\InvalidScopeException;
use yii\web\Request;

/**
 * Class GrantTypeIssue
 * @package app\modules\api\tokenizers\grant_types
 */
class GrantTypeIssue implements GrantTypeInterface {
	/**
	 * {@inheritdoc}
	 */
	public function loadRequest(Request $request):void {
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRefreshToken():?string {
		return null;
	}

	/**
	 * {@inheritdoc}
	 * @throws InvalidScopeException
	 */
	public function validate(UsersTokens $authToken, ?UsersTokens $refreshToken):void {
//		if ($authToken->isNewRecord) {
//			$statusIsOk = true;
//		} else {
//			$statusIsOk = (null === $refreshToken) ? !$authToken->isValid() : !$refreshToken->isValid();
//		}
//		if (!$statusIsOk) {
//			throw new InvalidScopeException();
//		}
	}
}