<?php

use app\models\managers\Managers;
use app\models\seller\invite_link\CreateSellerInviteLinkForm;
use app\models\seller\RegisterMiniSellerForm;
use app\models\seller\SellerInviteLink;
use app\models\seller\SellerMiniService;
use app\models\store\active_record\relations\RelStoresToUsers;
use app\models\store\Stores;
use app\models\sys\users\Users;
use app\models\tests\dealer\DealersTests;
use app\models\tests\MemoryDolApi;
use app\models\tests\store\StoresTests;
use app\models\tests\sys\permissions\PermissionsTest;
use app\models\tests\sys\users\SysUsersTest;
use app\modules\dol\models\DolAPI;
use pozitronik\relations\traits\RelationsTrait;
use yii\helpers\Url;

class SellersCest {
	public function _before(FunctionalTester $I) {
	}

	// tests
	public function assignMiniWithStore(FunctionalTester $I) {
		Yii::$container->setSingleton(DolAPI::class, function() {
			return new MemoryDolApi();
		});

		$service = new SellerMiniService();
		$user = SysUsersTest::create()->saveAndReturn();
		$user->withPermission(PermissionsTest::sellersAssignMiniWithStore('GET')->saveAndReturn());
		$user->withPermission(PermissionsTest::sellersAssignMiniWithStore('POST')->saveAndReturn());
		$user->withPermission(PermissionsTest::sellersIndex()->saveAndReturn());

		$store1 = StoresTests::create('Kitty #1')->saveAndReturn();
		$store2 = StoresTests::create('Kitty #2')->saveAndReturn();

		RelStoresToUsers::linkModel($store1, $user);
		RelStoresToUsers::linkModel($store2, $user);

		$service->createInviteLink(new CreateSellerInviteLinkForm([
			'phone_number' => '89055600901',
			'store_id' => $store1->id
		]));

		$miniSeller = new RegisterMiniSellerForm();
		$miniSeller->phone_number = '89055600901';
		$miniSeller->accept_agreement = true;
		$savedSeller = $service->register($miniSeller);

		$I->amLoggedInAs($user);
		$I->amOnRoute('sellers/assign-mini-with-store');
		$I->seeResponseCodeIs(200);
		$I->submitForm("#assign-mini-with-store", [
			'SellerMiniAssignWithStoreForm' => [
				'phone_number' => '+79055600901',
				'email' => '',
				'store_id' => $store2->id
			]
		]);
		$I->seeResponseCodeIs(200);
		$I->seeInCurrentUrl('sellers/index');
		$I->assertCount(1, $stores = $savedSeller->stores);
		$I->assertCount(0, SellerInviteLink::find()->all());
		/**
		 * @var Stores $store
		 */
		$store = $stores[0];
		$I->assertEquals("Kitty #2", $store->name);
	}
}
