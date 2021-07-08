<?php
declare(strict_types = 1);

namespace app\models\sys\users;

use app\models\phones\Phones;
use app\models\store\Stores;
use app\models\sys\permissions\traits\UsersPermissionsTrait;
use app\models\sys\users\active_record\Users as ActiveRecordUsers;
use Exception;
use pozitronik\filestorage\models\FileStorage;
use pozitronik\filestorage\traits\FileStorageTrait;
use pozitronik\helpers\PathHelper;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\IdentityInterface;

/**
 * Class Users
 * Авторизация, идентификация, доступы, прочие пользовательские функции, не относящиеся к ActiveRecord
 *
 * @property-read bool $isSaltedPassword Для удобства разрешено не использовать соль при установлении пароля
 * @property-read string $authKey @see [[yii\web\IdentityInterface::getAuthKey()]]
 *
 * Файловые атрибуты
 * @property mixed $avatar Картинка аватара пользователя (атрибут для загрузки)
 * @property-read null|FileStorage $fileAvatar Запись об актуальном файле аватара в файловом хранилище
 * @property-read string $currentAvatarUrl Шорткат для получения ссылки на актуальный файл аватарки
 */
class Users extends ActiveRecordUsers implements IdentityInterface {
	use UsersPermissionsTrait;
	use FileStorageTrait;

	public const DEFAULT_AVATAR_ALIAS_PATH = '@webroot/img/theme/avatar-m.png';

	public const DEFAULT_PASSWORD = 'Qq123456';

	/*файловые атрибуты*/
	public $avatar;

	private static ?self $_current = null;

	public function rules():array {
		return array_merge(parent::rules(), [
			[['avatar'], 'file', 'extensions' => 'png, jpg, jpeg', 'skipOnEmpty' => true],
			[['email'], 'email'],
			[['relatedPermissions', 'relatedPermissionsCollections'], 'safe']
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function attributeLabels():array {
		return array_merge(parent::attributeLabels(), [
			'relatedPermissions' => 'Прямые разрешения',
			'relatedPermissionsCollections' => 'Группы разрешений',
		]);
	}

	/**
	 * @return static
	 * @throws ForbiddenHttpException
	 */
	public static function Current():self {
		if ((null === self::$_current) && null === self::$_current = self::findIdentity(Yii::$app->user->id)) {
			throw new ForbiddenHttpException('Пользователь не авторизован');
		}

		return self::$_current;
	}

	/**
	 * @param string $login
	 * @return Users|null
	 */
	public static function findByLogin(string $login):?Users {
		return self::findOne(['login' => $login]);
	}

	/**
	 * @param string $email
	 * @return Users|null
	 */
	public static function findByEmail(string $email):?Users {
		return self::findOne(['email' => $email]);
	}

	/**
	 * @param string $restoreCode
	 * @return Users|null
	 */
	public static function findByRestoreCode(string $restoreCode):?Users {
		return self::findOne(['restore_code' => $restoreCode]);
	}

	/**
	 * @param string $phoneNumber
	 * @return Users|null
	 */
	public static function findByPhoneNumber(string $phoneNumber):?Users {
		if (null === $formattedNumber = Phones::defaultFormat($phoneNumber)) return null;
		return self::find()->joinWith(['relatedPhones'])->where(['phones.phone' => $formattedNumber])->one();
	}

	/**
	 * @inheritDoc
	 */
	public static function findIdentity($id) {
		return static::findOne($id);
	}

	/**
	 * Finds an identity by the given token.
	 * @param string $token the token to be looked for
	 * @param null|string $type the type of the token. The value of this parameter depends on the implementation.
	 * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
	 * @return IdentityInterface|null the identity object that matches the given token.
	 * Null should be returned if such an identity cannot be found
	 * or the identity is not in an active state (disabled, deleted, etc.)
	 * @throws Exception
	 */
	public static function findIdentityByAccessToken($token, $type = null):?IdentityInterface {
		return static::find()->joinWith('relatedUsersTokens rut')
			->where(['rut.auth_token' => $token])
			->andFilterWhere(['rut.type_id' => UsersTokens::getIdByType($type)])
			->one();
	}

	/**
	 * @return string
	 */
	public static function generateSalt():string {
		return sha1(uniqid((string)mt_rand(), true));
	}

	/**
	 * @param null|string $password
	 * @return string|null
	 */
	private function doSalt(?string $password):?string {
		return null === $password?null:sha1($password.$this->salt);
	}

	/**
	 * {@inheritDoc}
	 */
	public function beforeValidate():bool {
		if ($this->isNewRecord) {
			$this->password = $this->password??self::DEFAULT_PASSWORD;
			/*Если пользователь был создан админом без пароля, то ставим флаг принудительной смены пароля*/
			$this->is_pwd_outdated = $this->password === self::DEFAULT_PASSWORD;

		}
		if ($this->isAttributeUpdated('password')) {/*если пароль обновился, то пересолим*/
			$this->salt = self::generateSalt();
			$this->password = $this->doSalt($this->password);
		}
		return parent::beforeValidate();
	}

	/**
	 * @return string
	 * todo: unused
	 */
	public function getAuthKey():string {
		return md5($this->id.md5($this->login));
	}

	/**
	 * Returns an ID that can uniquely identify a user identity.
	 * @return int an ID that uniquely identifies a user identity.
	 */
	public function getId():int {
		return $this->id;
	}

	/**
	 * Validates the given auth key.
	 *
	 * @param string $authKey the given auth key
	 * @return bool whether the given auth key is valid.
	 * @see getAuthKey()
	 */
	public function validateAuthKey($authKey):bool {
		return $this->authKey === $authKey;
	}

	/**
	 * @return bool
	 */
	public function getIsSaltedPassword():bool {
		return null !== $this->salt;
	}

	/**
	 * Проверка пароля.
	 * Учитывает наличие соли; если соли нет, то сверяемся только с паролем (удобно для сброса УД прямо в БД)
	 * @param string $password password to validate
	 * @return bool if password provided is valid for current user
	 */
	public function validatePassword(string $password):bool {
		return $this->isSaltedPassword?$this->doSalt($password) === $this->password:$this->password === $password;
	}

	/**
	 * @return FileStorage|null
	 * @throws Throwable
	 */
	public function getFileAvatar():?FileStorage {
		return ([] === $files = $this->files(['avatar']))?null:ArrayHelper::getValue($files, 0);
	}

	/**
	 * @return string
	 * @throws Throwable
	 */
	public function getCurrentAvatarUrl():string {
		return (null === $fileAvatar = $this->fileAvatar)
			?PathHelper::PathToUrl(PathHelper::RelativePath(Yii::getAlias(self::DEFAULT_AVATAR_ALIAS_PATH), "@webroot"))
			:PathHelper::PathToUrl(PathHelper::RelativePath($fileAvatar->path, "@webroot"));
	}

	/**
	 * @return bool
	 */
	public function isManager():bool {
		$manager = $this->relatedManager;
		return null !== $manager;
	}

	/**
	 * @return bool
	 */
	public function isSeller():bool {
		$seller = $this->relatedSeller;
		return null !== $seller;
	}

	/**
	 * @return Stores[]
	 */
	public function getStoresViaRole():array {
		if ($this->isManager()) {
			return $this->relatedManager->stores;
		}
		if ($this->isSeller()) {
			return $this->relatedSeller->stores;
		}
		return $this->relatedStores;
	}
}
