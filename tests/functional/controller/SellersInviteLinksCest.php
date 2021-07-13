<?php
declare(strict_types = 1);
use app\components\exceptions\ValidateException;
use app\models\seller\invite_link\CreateSellerInviteLinkForm;
use app\models\seller\SellerInviteLink;
use app\models\seller\SellerInviteLinkSearch;
use app\models\seller\SellerMiniService;
use app\models\tests\MemoryDolApi;
use app\models\tests\store\StoresTests;
use app\models\tests\sys\permissions\PermissionsTest;
use app\models\tests\sys\users\SysUsersTest;
use app\modules\dol\models\DolAPI;
use Webmozart\Assert\Assert;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\helpers\Url;
use yii\mail\MessageInterface;

/**
 * Class InviteLinkCest
 */
class SellersInviteLinksCest {
	/**
	 * @var SysUsersTest $user
	 */
	protected $user;

	public function _before() {
		$user = SysUsersTest::create()->changeUsername('manager')->saveAndReturn();
		$user->withPermission(PermissionsTest::sellersInviteLinksCreate('GET')->saveAndReturn());
		$user->withPermission(PermissionsTest::sellersInviteLinksCreate('POST')->saveAndReturn());
		$user->withPermission(PermissionsTest::sellersInviteLinksEdit('POST')->saveAndReturn());
		$user->withPermission(PermissionsTest::sellersInviteLinksEdit('GET')->saveAndReturn());
		$user->withPermission(PermissionsTest::sellersInviteLinksIndex()->saveAndReturn());

		$this->user = $user;

	}

	// tests

	/**
	 * @param FunctionalTester $I
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
	 */
	public function create(FunctionalTester $I) {
		$I->amLoggedInAs($this->user);
		Yii::$container->setSingleton(DolAPI::class, function() {
			return new MemoryDolApi();
		});
		$store = StoresTests::create("Kitty")->saveAndReturn();

		$I->amOnRoute($createUrl = 'sellers-invite-links/create');
		$I->seeResponseCodeIs(200);
		$I->submitForm('#create-seller-invite-link', $submitForm = [
			'CreateSellerInviteLinkForm' => [
				'phone_number' => '89055600901',
				'email' => $emailSended = 'example@dev.com',
				'store_id' => $store->id
			]
		]);
		$I->seeResponseCodeIs(200);
		$I->seeInCurrentUrl('sellers-invite-links/index');
		$I->seeResponseCodeIs(200);
		$I->seeEmailIsSent(1);

		$I->assertCount(1, $inviteLinks = (new SellerInviteLinkSearch())->all());

		$date = new DateTime();
		$date->add(new DateInterval('P1D'));

		/**
		 * @var SellerInviteLink $link
		 */
		$link = $inviteLinks[0];
		$I->assertEquals($store->id, $link->store_id);
		$I->assertEquals($phoneWithFormat = "+79055600901", $link->phone_number);
		$I->assertEquals($emailSended, $link->email);

		$expiredAt = new DateTime($link->expired_at);
		$I->assertEquals($date->format('Y-m-d H:i'), $expiredAt->format('Y-m-d H:i'));
		$I->assertNotNull($link->token);
		Assert::minLength($link->token, 5, 'Неправильная минимальная длина токена');

		$smsService = Yii::$container->get(DolAPI::class);
		/**
		 * @var MemoryDolApi $smsService
		 */
		$I->assertCount(1, $smses = $smsService->smses);
		$I->assertEquals($phoneWithFormat, $smses[0]['phone']);
		$I->assertEquals("Ваша ссылка: ".$link->inviteUrl(), $smses[0]['message']);

		/**
		 * @var MessageInterface $message
		 */
		$message = $I->grabLastSentEmail();
		$I->assertArrayHasKey($emailSended, $message->getTo());

		$I->amOnRoute($createUrl);
		$I->seeResponseCodeIs(200);
		$I->submitForm('#create-seller-invite-link', $submitForm);
		$I->seeInCurrentUrl($createUrl);
	}

	/**
	 * @param FunctionalTester $I
	 * @throws Throwable
	 * @throws ValidateException
	 * @throws \yii\base\Exception
	 */
	public function edit(FunctionalTester $I) {
		$I->amLoggedInAs($this->user);
		$store = StoresTests::create("Kitty")->saveAndReturn();
		$createLink = new CreateSellerInviteLinkForm([
			'phone_number' => '89055600902',
			'email' => 'devok@example.com',
			'store_id' => $store->id
		]);
		$savedLink = (new SellerMiniService())->createInviteLink($createLink);

		$I->amOnRoute($editLink = Url::toRoute(['/sellers-invite-links/edit', 'id' => $savedLink->id]));
		$I->seeResponseCodeIs(200);
		$I->submitForm('#edit-seller-invite-link', [
			'EditSellerInviteLink' => [
				'existentIdLink' => $savedLink->id,
				'phone_number' => '89055600902',
				'email' => $email = 'devok@example.com'
			]
		]);
		$I->seeResponseCodeIs(200);
		$I->seeInCurrentUrl($editLink);
		$I->assertCount(1, $links = (new SellerInviteLinkSearch())->all());

		/**
		 * @var SellerInviteLink $link
		 */
		$link = $links[0];
		$I->assertEquals("+79055600902", $link->phone_number);
		$I->assertEquals($email, $link->email);
	}
}
