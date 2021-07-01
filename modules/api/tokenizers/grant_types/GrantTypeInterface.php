<?php
declare(strict_types = 1);

namespace app\modules\api\tokenizers\grant_types;

use app\models\sys\users\UsersTokens;
use yii\web\Request;

/**
 * Interface GrantTypeInterface
 * @package app\modules\api\tokenizers\grant_types
 */
interface GrantTypeInterface {
	/**
	 * @param Request $request
	 */
	public function loadRequest(Request $request):void;

	/**
	 * @return string|null
	 */
	public function getRefreshToken():?string;

	/**
	 * Проверка наличия ограничений на характер запроса токена.
	 * @param UsersTokens $authToken
	 * @param UsersTokens|null $refreshToken
	 */
	public function validate(UsersTokens $authToken, ?UsersTokens $refreshToken):void;
}