<?php
declare(strict_types = 1);
use Codeception\Actor;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends Actor {
	use _generated\FunctionalTesterActions;

	/**
	 * @param callable $function
	 * @return bool
	 */
	public function expectDomainException(callable $function) {
		try {
			$function();
			return false;
		} catch (Exception $e) {
			if (get_class($e) == DomainException::class) {
				return true;
			}
			return false;
		}
	}

	/**
	 * @param $body
	 */
	public function sendGraphQlRequest($body) {
		$this->sendPost(Url::toRoute(['/graphql']), [
			'query' => $body
		]);
	}

	/**
	 * @param string $login
	 * @param string $pass
	 */
	public function authInApi(string $login, string $pass) {
		$this->amHttpAuthenticated($login, $pass);
		$this->sendPost(Url::to(['/api/auth/token']), [
			'grant_type' => 'client_credentials'
		]);
		$this->seeResponseCodeIs(200);
	}

	/**
	 * @return array
	 */
	public function grapAuthTokens() {
		$this->seeResponseJsonMatchesJsonPath("$.access_token");
		$this->seeResponseJsonMatchesJsonPath("$.expires_in");
		$this->seeResponseJsonMatchesJsonPath("$.refresh_token");
		$response = Json::decode($this->grabResponse());

		return [
			$response['access_token'],
			$response['expires_in'],
			$response['refresh_token']
		];
	}
}
