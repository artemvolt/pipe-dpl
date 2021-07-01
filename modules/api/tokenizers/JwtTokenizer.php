<?php
declare(strict_types = 1);

namespace app\modules\api\tokenizers;

use cusodede\jwt\JwtHttpBearerAuth;
use DateTimeImmutable;
use Yii;

/**
 * Class JwtTokenizer
 * @package app\modules\api\tokenizers
 */
class JwtTokenizer extends OAuthTokenizer {
	/**
	 * {@inheritdoc}
	 */
	protected function getTokenType():string {
		return JwtHttpBearerAuth::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthToken():string {
		$iat = new DateTimeImmutable($this->_authToken->created);
		$exp = new DateTimeImmutable($this->_authToken->valid);
		$jti = $this->_authToken->auth_token;

		$token = Yii::$app->jwt->getBuilder()
			->issuedBy(Yii::$app->name)
			->issuedAt($iat)
			->identifiedBy($jti)
			->expiresAt($exp)
			->getToken(
				Yii::$app->jwt->configuration->signer(),
				Yii::$app->jwt->configuration->signingKey()
			);

		return $token->toString();
	}
}