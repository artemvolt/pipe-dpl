<?php
declare(strict_types = 1);
use app\components\exceptions\ValidateException;
use app\models\seller\invite_link\CreateSellerInviteLinkForm;
use app\models\seller\RegisterMiniSellerForm;
use app\models\seller\SellerInviteLinkSearch;
use app\models\seller\SellerMiniService;
use app\models\seller\SellersSearch;
use app\models\tests\MemoryDolApi;
use app\models\tests\store\StoresTests;
use app\modules\dol\components\exceptions\ServerDomainError;
use app\modules\dol\models\DolAPI;
use Codeception\Stub;
use yii\helpers\Json;
use yii\httpclient\Response;

/**
 * Class SalesCest
 */
class SellerCest {
	/**
	 * @param FunctionalTester $I
	 */
	public function registerMini(FunctionalTester $I) {
		Yii::$container->setSingleton(DolAPI::class, function() {
			return new MemoryDolApi();
		});

		$I->sendPost($registerUrl = '/api/seller/register', $postData = [
			'phone_number' => "89061601001",
			"accept_agreement" => true
		]);

		$I->seeResponseContainsJson([
			'data' => [
				'result' => true
			]
		]);
		$response = Json::decode($I->grabResponse());
		$I->assertArrayHasKey('expiredAt', $response['data']);
		$I->assertNotEmpty($response['data']['expiredAt']);

		$search = new SellersSearch();
		$I->assertCount(1, $sellers = $search->findAllRows());

		$seller = $sellers[0];
		$I->assertNull($seller->surname);
		$I->assertNull($seller->name);
		$I->assertNull($seller->patronymic);
		$I->assertNotNull($seller->relatedUser);
		$I->assertEquals('89061601001', $seller->relatedUser->login);
		$I->assertEquals('89061601001', $seller->relatedUser->username);
		$I->assertNull($seller->relatedUser->email);
		$I->assertCount(1, $phones = $seller->getPhonesNumbers());
		$I->assertEquals("+79061601001", $phones[0]->phone);
		$I->assertTrue($seller->isUnActiveStatus());

		/**
		 * @var MemoryDolApi $smss
		 */
		$smss = Yii::$container->get(DolAPI::class);

		$I->assertCount(1, $registerPhones = $smss->register);
		$I->assertEquals('89061601001', $registerPhones[0]['phone']);

		$I->expectThrowable(ValidateException::class, function() use ($I, $registerUrl, $postData) {
			$I->sendPost($registerUrl, $postData);
		});
	}

	/**
	 * @param FunctionalTester $I
	 */
	public function registerMiniWithValidateError(FunctionalTester $I) {
		$I->expectThrowable(ValidateException::class, function() use ($I) {
			$I->sendPost('/api/seller/register', [
				'phone_number' => "890616withInvalid",
				"accept_agreement" => true
			]);
		});
	}

	/**
	 * @param FunctionalTester $I
	 * @skip
	 */
	public function checkCode(FunctionalTester $I) {
		Yii::$container->setSingleton(DolAPI::class, function() {
			return new MemoryDolApi();
		});

		$service = new SellerMiniService();
		$form = new RegisterMiniSellerForm();
		$form->phone_number = $phone = '89055600901';
		$form->accept_agreement = true;
		$seller = $service->register($form);

		$I->expectThrowable(ServerDomainError::class, function() use ($I, $phone) {
			$I->sendPost('/api/seller/confirm-code', [
				'phone_number' => $phone,
				'sms' => 444,
				'token' => Yii::$app->security->generateRandomString()
			]);
		});

		$I->sendPost('/api/seller/confirm-code', [
			'phone_number' => $phone,
			'sms' => 4444,
			'verificationToken' => $seller->getVerificationToken()
		]);
		$I->seeResponseContainsJson([
			'data' => [
				'result' => true
			]
		]);
	}

	/**
	 * @param FunctionalTester $I
	 */
	public function inviteLink(FunctionalTester $I) {
		$I->sendGraphQlRequest('
query {
	seller {
		inviteLink(token: "123") {
			phone_number,
			email
		}
	}
}
');
		$I->seeResponseContainsJson([
			'data' => [
				'seller' => [
					'inviteLink' => [
						'phone_number' => null,
						'email' => null
					]
				]
			]
		]);

		$store = StoresTests::create('Kitty')->saveAndReturn();
		$form = new CreateSellerInviteLinkForm([
			'phone_number' => '+79055600901',
			'email' => 'a@a.ru',
			'store_id' => $store->id
		]);
		$service = new SellerMiniService();
		$savedLink = $service->createInviteLink($form);

		$I->sendGraphQlRequest('
query {
	seller {
		inviteLink(token: "'.$savedLink->token.'") {
			phone_number,
			email
		}
	}
}
');
		$I->seeResponseContainsJson([
			'data' => [
				'seller' => [
					'inviteLink' => [
						'phone_number' => $savedLink->phone_number,
						'email' => $savedLink->email
					]
				]
			]
		]);
	}

	/**
	 * @param FunctionalTester $I
	 * @skip
	 */
	public function confirmSms(FunctionalTester $I) {
		Yii::$container->setSingleton(DolAPI::class, function() {
			return new MemoryDolApi();
		});

		$store = StoresTests::create("Kitty #1")->saveAndReturn();
		$service = new SellerMiniService();
		$service->createInviteLink(new CreateSellerInviteLinkForm([
			'phone_number' => '89055600901',
			'store_id' => $store->id
		]));
		$service->register(new RegisterMiniSellerForm([
			'phone_number' => '89055600901',
			'accept_agreement' => true
		]));

		$I->sendGraphQlRequest('
mutation {
	seller {
		confirmSms(phone_number:"89055600901", sms: "456") {
			...on Response{
    			result,
                message,
                errors{field, messages}
    		}
		}
	}
}
		');
		$I->seeResponseContainsJson([
			'data' => [
				'seller' => [
					'confirmSms' => [
						'result' => true,
					]
				]
			]
		]);
		$I->assertCount(0, SellerInviteLinkSearch::find()->all());
	}

	/**
	 * @param FunctionalTester $I
	 * @skip
	 */
	public function confirmSmsErrors(FunctionalTester $I) {
		$I->sendGraphQlRequest('
mutation {
	seller {
		confirmSms(phone_number: "99999999aa", sms: "1238") {
			...on Response{
    			result,
                message,
                errors{field, messages}
    		}
		}
	}
}
		');
		$I->seeResponseContainsJson([
			'data' => [
				'seller' => [
					'confirmSms' => [
						'result' => false,
						'errors' => [
							['field' => 'phone_number'],
						]
					]
				]
			]
		]);
	}

	/**
	 * @param FunctionalTester $I
	 * @skip
	 */
	public function confirmSmsValidateServerErrors(FunctionalTester $I) {
		Yii::$container->setSingleton(DolAPI::class, function() {
			return new MemoryDolApi();
		});

		$store = StoresTests::create("Kitty #1")->saveAndReturn();
		$service = new SellerMiniService();
		$service->createInviteLink(new CreateSellerInviteLinkForm([
			'phone_number' => '89055600901',
			'store_id' => $store->id
		]));
		$service->register(new RegisterMiniSellerForm([
			'phone_number' => '89055600901',
			'accept_agreement' => true
		]));

		Yii::$container->setSingleton(DolAPI::class, function() {
			return Stub::make(DolAPI::class, [
				'doRequest' => function() {
					return new Response([
						'content' => json_encode([
							'errors' => [
								'phoneAsLogin' => ['1', '2'],
								'Code' => ['3', '4']
							]
						])
					]);
				}
			]);
		});

		$I->sendGraphQlRequest('
mutation {
	seller {
		confirmSms(phone_number: "89055600901", sms: "4578") {
			...on Response{
    			result,
                message,
                errors{field, messages}
    		}
		}
	}
}
		');
		$I->seeResponseContainsJson([
			'data' => [
				'seller' => [
					'confirmSms' => [
						'result' => false,
						'errors' => [
							['field' => 'phone_number', 'messages' => ['1', '2']],
							['field' => 'sms', 'messages' => ['3', '4']]
						]
					]
				]
			]
		]);
	}

}
