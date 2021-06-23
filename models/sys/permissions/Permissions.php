<?php
declare(strict_types = 1);

namespace app\models\sys\permissions;

use app\models\core\CacheHelper;
use app\models\sys\permissions\active_record\Permissions as ActiveRecordPermissions;
use pozitronik\helpers\ArrayHelper;
use Throwable;
use Yii;
use yii\caching\TagDependency;

/**
 * Class Permissions
 * todo:
 * 3) Генератор разрешений (консольный)
 * 4) Флаг deleted
 */
class Permissions extends ActiveRecordPermissions {
	/*Любое из перечисленных прав*/
	public const LOGIC_OR = 0;
	/*Все перечисленные права*/
	public const LOGIC_AND = 1;
	/*Ни одно из перечисленных прав*/
	public const LOGIC_NOT = 2;

	/*Минимальный/максимальный приоритет*/
	public const PRIORITY_MIN = 0;
	public const PRIORITY_MAX = 100;

	/*Параметры разрешения, для которых пустой фильтр приравнивается к любому значению*/
	public const ALLOWED_EMPTY_PARAMS = ['action', 'verb'];

	public const COMPONENT_NAME = 'permissions';
	public const GRANT_ALL = 'grantAll';
	public const CONTROLLER_DIRS = 'controllerDirs';
	/*Название параметра с преднастроенными правилами доступов*/
	public const CONFIGURATION_PERMISSIONS = 'permissions';
	/*Перечисление назначений конфигураций через конфиги, id => ['...', '...']*/
	public const GRANT_PERMISSIONS = 'grant';

	/**
	 * Возвращает значение конфига для компонента
	 * @param string $parameter
	 * @param mixed $default
	 * @return mixed
	 * @throws Throwable
	 * @noinspection PhpReturnDocTypeMismatchInspection Проверено, в $default мы можем задать что угодно
	 */
	public static function ConfigurationParameter(string $parameter, $default = null) {
		return ArrayHelper::getValue(Yii::$app->components, self::COMPONENT_NAME.".".$parameter, $default);
	}

	/**
	 * Вернуть список преднастроенных правил из конфига
	 * @return self[]
	 * @throws Throwable
	 */
	public static function GetConfigurationPermissions(?array $filter = null):array {
		$permissionsConfig = self::ConfigurationParameter(self::CONFIGURATION_PERMISSIONS, []);
		if (null !== $filter) $permissionsConfig = ArrayHelper::filter($permissionsConfig, $filter);
		$result = [];
		/*convert to models*/
		foreach ($permissionsConfig as $name => $permissionConfig) {
			$permissionConfig['name'] = $name;
			$result[] = (new self($permissionConfig))->attributes;
		}
		return $result;
	}

	/**
	 * Все доступы пользователя из БД
	 * @param int $user_id
	 * @param string[] $permissionFilters
	 * @param bool $asArray
	 * @return self[]
	 */
	public static function allUserPermissions(int $user_id, array $permissionFilters = [], bool $asArray = true):array {
		$query = self::find()
			->distinct()
			->joinWith(['relatedUsersToPermissions directPermissions', 'relatedUsersToPermissionsCollections collectionPermissions'], false)
			->where(['directPermissions.user_id' => $user_id])
			->orWhere(['collectionPermissions.user_id' => $user_id])
			->orderBy([
				'priority' => SORT_DESC,
				'id' => SORT_ASC
			]);
		foreach ($permissionFilters as $paramName => $paramValue) {
			$paramValues = [$paramValue];
			/*для перечисленных параметров пустое значение приравнивается к любому*/
			if (in_array($paramName, self::ALLOWED_EMPTY_PARAMS, true)) {
				$paramValues[] = null;
			}
			$query->andWhere([self::tableName().".".$paramName => $paramValues]);

		}
		return $query->asArray($asArray)->all();
	}

	/**
	 * Все доступы пользователя из конфига (без фильтрации, просто всё, что назначено)
	 * @param int $user_id
	 * @return self[]
	 * @throws Throwable
	 * @throws Throwable
	 */
	public static function allUserConfigurationPermissions(int $user_id /*, array $permissionFilters = [], bool $asArray = true*/ /*todo*/):array {
		/** @var array $userConfigurationGrantedPermissions */
		$userConfigurationGrantedPermissions = ArrayHelper::getValue(self::ConfigurationParameter(self::GRANT_PERMISSIONS, []), $user_id, []);
		return self::GetConfigurationPermissions($userConfigurationGrantedPermissions);
	}

	/**
	 * При изменении права, нужно удалить кеши прав всем пользователям, у которых:
	 *    - право назначено напрямую
	 *    - право есть в  группе прав, назначенной пользователю
	 * @inheritDoc
	 */
	public function afterSave($insert, $changedAttributes):void {
		if (false === $insert && [] !== $changedAttributes) {
			$usersIds = array_unique(array_merge(
				ArrayHelper::getColumn($this->relatedUsers, 'id'),
				ArrayHelper::getColumn($this->relatedUsersViaPermissionsCollections, 'id')
			));

			foreach ($usersIds as $userId) {
				TagDependency::invalidate(Yii::$app->cache, [CacheHelper::MethodSignature('Users::allPermissions', ['id' => $userId])]);
			}
		}
		parent::afterSave($insert, $changedAttributes);
	}
}