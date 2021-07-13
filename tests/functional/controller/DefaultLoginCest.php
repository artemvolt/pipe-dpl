<?php

use app\models\tests\sys\users\SysUsersTest;

class DefaultLoginCest {
	/**
	 * @param FunctionalTester $I
	 */
	public function admin(FunctionalTester $I) {
		$admin = SysUsersTest::createAdmin()->saveAndReturn();

		$I->amOnPage(['home/home']);
		$I->seeResponseCodeIs(403);
		$I->canSee("Пользователь не авторизован");

		$I->amOnPage(["site/login"]);
		$I->seeResponseCodeIs(200);
		$I->seeInTitle("Вход");
		$I->canSee("Логин");
		$I->submitForm("#login_form", [
			"LoginForm[login]" => 'admin',
			"LoginForm[password]" => 'admin',
			"LoginForm[rememberMe]" => '1',
		]);
		$I->seeInCurrentUrl('home/home');
	}
}