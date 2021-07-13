<?php

use app\controllers\SiteController;
use app\controllers\UsersController;
use app\models\tests\sys\permissions\PermissionsTest;
use app\models\tests\sys\users\SysUsersTest;
use app\modules\history\models\HistorySearch;
use yii\helpers\Url;

class LoginAsAnotherUserCest {

	/**
	 * @param FunctionalTester $I
	 */
	public function fail(FunctionalTester $I) {
		$admin = SysUsersTest::createAdmin()->saveAndReturn();
		$user = SysUsersTest::create()->changeUsername('user')->saveAndReturn();

		$I->amLoggedInAs($admin);
		$I->amOnPage(['users/index']);
		$I->cantSeeLink($loginAsUrl = Url::toRoute(['users/login-as-another-user', 'userId' => $user->id]));
		$I->expectDomainException(function() use ($I, $loginAsUrl) {
			$I->amOnPage($loginAsUrl);
		});
	}

	/**
	 * @param FunctionalTester $I
	 */
	public function success(FunctionalTester $I) {
		$admin = SysUsersTest::createAdmin()->saveAndReturn();
		$admin->withPermission(PermissionsTest::loginAsAnotherUser()->saveAndReturn());

		$user = SysUsersTest::create()->changeUsername('user')->saveAndReturn();
		$user->withPermission(PermissionsTest::loginBack()->saveAndReturn());
		Yii::$app->cache->flush();

		$I->amLoggedInAs($admin);
		$I->amOnPage(['users/login-as-another-user?userId='.$user->id]);
		$I->seeResponseCodeIs(200);
		$I->seeInCurrentUrl(Url::toRoute(['home/home']));

		$I->seeCookie('fear');
		$I->assertIsNotInt($_COOKIE['fear']);
		$I->assertNotEquals($admin->id, $_COOKIE['fear']);

		HistorySearch::deleteAll();

		$user->changeUsername('hello321')->saveAndReturn();

		$I->assertCount(0, HistorySearch::find()->where([
			'user' => $user->id,
			'delegate' => $user->id,
			'model_class' => SysUsersTest::class
		])->all());

		$I->assertCount(1, HistorySearch::find()->where([
			'user' => $user->id,
			'delegate' => $admin->id,
			'model_class' => SysUsersTest::class
		])->all());

		$I->amOnPage(['users/login-back']);
		$I->seeResponseCodeIs(200);
		$I->seeInCurrentUrl(Url::toRoute(['home/home']));
		$I->dontSeeCookie('fear');

		HistorySearch::deleteAll();

		$admin->changeUsername('hello321')->saveAndReturn();

		$I->assertCount(1, HistorySearch::find()->where([
			'user' => $admin->id,
			'delegate' => null,
			'model_class' => SysUsersTest::class
		])->all());
	}
}
