<?php

use app\models\tests\sys\permissions\PermissionsTest;
use app\models\tests\sys\users\SysUsersTest;

class VersionControllerCest {
	public function _before(FunctionalTester $I) {
	}

	// tests
	public function index(FunctionalTester $I) {
		$admin = SysUsersTest::createAdmin()->saveAndReturn();
		$admin->withPermission(PermissionsTest::apiAuthToken()->saveAndReturn());
		$admin->withPermission(PermissionsTest::apiVersionIndex()->saveAndReturn());

		$I->authInApi("admin", "admin");
		[$accessToken] = $I->grapAuthTokens();

		$I->amBearerAuthenticated($accessToken);
		$I->sendPost('/api/version/index');
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			'version' => "1.0"
		]);
	}
}
