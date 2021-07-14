<?php
declare(strict_types = 1);

namespace app\models\tests\sys\users;

use app\models\managers\Managers;
use app\models\sys\permissions\active_record\relations\RelUsersToPermissions;
use app\models\sys\permissions\Permissions;
use app\models\sys\users\Users;
use app\models\tests\core\prototypes\ActiveRecordTraitTest;
use DomainException;
use Faker\Factory;
use Webmozart\Assert\Assert;
use Yii;
use yii\base\Exception;

/**
 * Class SysUsersTest
 * @package app\models\tests\sys\users
 */
class SysUsersTest extends Users {

	use ActiveRecordTraitTest;

	public function changeUsername(string $username):self {
		Assert::notEmpty($username);
		$this->username = $username;
		return $this;
	}

	public static function createAdmin():self {
		$self = new self();
		$self->login = 'admin';
		$self->username = 'admin';
		$self->password = 'admin';
		$self->email = 'admin@admin.ru';
		$self->comment = 'Системный администратор';
		$self->create_date = date('Y-m-d');
		$self->salt = null;
		return $self;
	}

	/**
	 * @param string $pass
	 * @return SysUsersTest
	 * @throws Exception
	 */
	public function changePassword(string $pass):self {
		$this->salt = Yii::$app->security->generateRandomString();
		$this->password = Yii::$app->security->generatePasswordHash($pass);
		return $this;
	}

	public static function create():self {
		$faker = Factory::create();

		$self = new self();
		$self->username = $faker->userName;
		$self->login = $self->username;
		$self->password = Yii::$app->security->generateRandomString();
		$self->email = $faker->email;
		$self->comment = $faker->name;
		$self->create_date = date('Y-m-d');
		$self->salt = Yii::$app->security->generateRandomString();
		return $self;
	}

	public function withPermission(Permissions $permission):self {
		$relation = new RelUsersToPermissions();
		$relation->user_id = $this->id;
		$relation->permission_id = $permission->id;
		if (!$relation->save()) {
			throw new DomainException("h");
		}
		return $this;
	}

	/**
	 * @param Managers $manager
	 * @return $this
	 */
	public function assignWithManager(Managers $manager):SysUsersTest {
		$manager->user = $this->id;
		if (!$manager->save()) throw new DomainException("Не получилось сохранить");
		return $this;
	}
}