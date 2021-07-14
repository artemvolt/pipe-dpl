<?php

namespace models\sys\permissions;

use app\models\sys\permissions\active_record\relations\RelPermissionsCollectionsToPermissions;
use app\models\sys\permissions\active_record\relations\RelPermissionsCollectionsToPermissionsCollections;
use app\models\sys\permissions\active_record\relations\RelUsersToPermissions;
use app\models\sys\permissions\active_record\relations\RelUsersToPermissionsCollections;
use app\models\tests\sys\permissions\PermissionsCollectionsTests;
use app\models\tests\sys\users\SysUsersTest;
use Codeception\Test\Unit;
use app\models\tests\sys\permissions\PermissionsTest as PermissionsModelTest;
use UnitTester;

class PermissionsTest extends Unit {
	/**
	 * @var UnitTester
	 */
	protected $tester;

	protected function _before() {
	}

	protected function _after() {
	}

	public function testHadPermissionWithOutCollection() {
		$user = SysUsersTest::create()->changeUsername("manager")->saveAndReturn();
		$permission = PermissionsModelTest::create("seller", "index", "GET")->saveAndReturn();
		RelUsersToPermissions::linkModel($user, $permission);

		$this->assertTrue($user->hasPermission(["seller_index_get"]));
		$this->assertTrue($user->hasControllerPermission("seller", "index", "GET"));
	}

	// tests
	public function testHasPermission() {
		$director = SysUsersTest::create()->changeUsername('director')->saveAndReturn();
		$manager = SysUsersTest::create()->changeUsername('manager')->saveAndReturn();

		$directorCollection = PermissionsCollectionsTests::create("director")->saveAndReturn();
		$managerCollection = PermissionsCollectionsTests::create("manager")->saveAndReturn();

		RelUsersToPermissionsCollections::linkModel($director, $directorCollection);
		RelUsersToPermissionsCollections::linkModel($manager, $managerCollection);

		$controllerDirector = PermissionsModelTest::create("director", "list", "GET")->saveAndReturn();
		$controllerManager = PermissionsModelTest::create("manager", "list")->saveAndReturn();
		RelPermissionsCollectionsToPermissions::linkModel($directorCollection, $controllerDirector);
		RelPermissionsCollectionsToPermissions::linkModel($managerCollection, $controllerManager);

		RelPermissionsCollectionsToPermissionsCollections::linkModel($directorCollection, $managerCollection);

		$this->assertTrue($director->hasPermission(['director_list_get']));
		$this->assertTrue($director->hasPermission(['manager_list']));
		$this->assertTrue($director->hasControllerPermission('director', 'list', 'GET'));
		$this->assertTrue($director->hasControllerPermission('manager', 'list'));
	}
}