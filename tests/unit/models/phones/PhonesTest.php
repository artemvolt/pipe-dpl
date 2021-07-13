<?php

namespace models\phones;

use app\models\phones\Phones;
use Codeception\Test\Unit;
use UnitTester;

/**
 * Class PhonesTest
 * @package models\phones
 */
class PhonesTest extends Unit {
	/**
	 * @var UnitTester
	 */
	protected $tester;

	protected function _before() {
	}

	protected function _after() {
	}

	// tests
	public function testNationalFormat() {
		$this->assertEquals("9061601001", Phones::nationalFormat("+79061601001"));
		$this->assertEquals("9061601001", Phones::nationalFormat("89061601001"));
	}
}