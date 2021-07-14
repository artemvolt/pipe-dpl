<?php

use app\models\tests\sys\permissions\PermissionsTest;
use app\models\tests\sys\users\SysUsersTest;
use Webmozart\Assert\Assert;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Class AuthControllerCest
 */
class AuthControllerCest {
	public function _before(FunctionalTester $I) {
	}

	// tests
	public function token(FunctionalTester $I) {
		$admin = SysUsersTest::createAdmin()->saveAndReturn();
		$admin->withPermission(PermissionsTest::apiAuthToken()->saveAndReturn());

		$I->authInApi("admin", "admin");
		$I->seeResponseJsonMatchesJsonPath("$.access_token");
		$I->seeResponseJsonMatchesJsonPath("$.expires_in");
		$I->seeResponseJsonMatchesJsonPath("$.refresh_token");
		$response = Json::decode($I->grabResponse());
		Assert::minLength($response['access_token'], 5);
		Assert::integer($response['expires_in']);
		Assert::minLength($response['refresh_token'], 5);
	}
}
