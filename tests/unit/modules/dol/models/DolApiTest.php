<?php

namespace modules\dol\models;

use app\modules\dol\components\exceptions\ServerDomainError;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use app\modules\dol\models\DolAPI;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Throwable;
use UnitTester;
use yii\base\InvalidConfigException;
use yii\httpclient\Response;

class DolApiTest extends Unit {
	/**
	 * @var UnitTester
	 */
	protected $tester;

	protected function _before() {
	}

	protected function _after() {
	}

	// tests
	public function testConfirmSmsLogonWithValdiateErrors() {
		$dolApi = $this->makeDol([
			'type' => 'hello',
			'errors' => [
				'Code' => [
					"Код подтверждения хз"
				],
				'PhoneAsLogin' => [
					"ЖЖ"
				]
			]
		]);
		$catch = false;
		try {
			$dolApi->confirmSmsLogon("789989", '123');
		} catch (Throwable $e) {
			$catch = true;
			$this->assertEquals(ValidateServerErrors::class, get_class($e));
			/**
			 * @var ValidateServerErrors $e
			 */
			$this->assertNotEmpty($errors = $e->getErrors());
			$this->assertArrayHasKey('Code', $errors);
			$this->assertArrayHasKey('PhoneAsLogin', $errors);
		}
		$this->assertTrue($catch);
	}

	public function testConfirmSmsLogonWithServerErrors() {
		$dolApi = $this->makeDol([
			'success' => false,
			'isTimeout' => false,
			'errorMessage' => 'Something wrong'
		]);
		$catch = false;
		try {
			$dolApi->confirmSmsLogon("789989", '123');
		} catch (Throwable $e) {
			$catch = true;
			$this->assertEquals(ServerDomainError::class, get_class($e));
			/**
			 * @var ServerDomainError $e
			 */
			$this->assertEquals('Something wrong', $e->getMessage());
		}
		$this->assertTrue($catch);
	}

	public function testConfirmSmsLogonWithTimeoutErrors() {
		$dolApi = $this->makeDol([
			'success' => false,
			'isTimeout' => true,
			'errorMessage' => null
		]);
		$catch = false;
		try {
			$dolApi->confirmSmsLogon("789989", '123');
		} catch (Throwable $e) {
			$catch = true;
			$this->assertEquals(ServerDomainError::class, get_class($e));
			/**
			 * @var ServerDomainError $e
			 */
			$this->assertEquals('Истекло время ожидания для подтверждение смс. Запросите повторно.', $e->getMessage());
		}
		$this->assertTrue($catch);
	}

	public function testConfirmSmsLogonSuccess() {
		$dolApi = $this->makeDol([
			'success' => true,
			'isTimeout' => false,
			'errorMessage' => null
		]);
		$this->assertNotEmpty($dolApi->confirmSmsLogon("789989", '123'));
	}

	/**
	 * @param array $response
	 * @return DolAPI
	 * @throws Exception
	 */
	protected function makeDol(array $response) {
		return Stub::make(DolAPI::class, [
			'doRequest' => function() use ($response) {
				return new Response(['content' => json_encode($response)]);
			}
		]);
	}
}