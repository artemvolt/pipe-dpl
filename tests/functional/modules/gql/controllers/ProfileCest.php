<?php

use app\models\seller\RegisterMiniSellerForm;
use app\models\seller\Sellers;
use app\models\tests\sys\permissions\PermissionsTest;

class ProfileCest {
	public function _before(FunctionalTester $I) {
	}

	/**
	 * @param FunctionalTester $I
	 * @skip
	 */
	public function defaultInfo(FunctionalTester $I) {
		$case = new RegisterMiniSellerForm([
			'surname' => 'Пушкин',
			'name' => 'Александр',
			'patronymic' => 'Сергеевич',
			'phone_number' => '89055600901',
			'email' => 'domain@example.com'
		]);
		$savedSeller = $case->register();
		$savedSeller->relatedUser->changePassword("hello")->saveAndReturn();
		$savedSeller->relatedUser->withPermission(PermissionsTest::apiAuthToken()->saveAndReturn());

		$I->authInApi("89055600901", "hello");
		[$token] = $I->grapAuthTokens();

		$I->amBearerAuthenticated($token);
		$I->sendGraphQlRequest('
		query {
			profile {
				first_name,
				middle_name,
				last_name,
				login,
				email
			}
		}
		');
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			'data' => [
				'profile' => [
					'first_name' => 'Пушкин',
					'name' => 'Александр',
					'middle_name' => 'Сергеевич',
					'login' => '89055600901',
					'email' => 'domain@example.com'
				]
			]
		]);
	}
}
