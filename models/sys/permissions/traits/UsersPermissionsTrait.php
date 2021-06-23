<?php
declare(strict_types = 1);

namespace app\models\sys\permissions\traits;

use app\models\core\CacheHelper;
use app\models\sys\permissions\active_record\relations\RelUsersToPermissions;
use app\models\sys\permissions\active_record\relations\RelUsersToPermissionsCollections;
use app\models\sys\permissions\Permissions;
use app\models\sys\permissions\PermissionsCollections;
use pozitronik\helpers\ArrayHelper;
use Throwable;
use Yii;
use yii\base\Action;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class Permissions
 * Управление правами доступа
 * @property int $id Model primary key attribute name
 *
 * @property RelUsersToPermissions[] $relatedUsersToPermissions Связь к промежуточной таблице пользовательских доступов
 * @property RelUsersToPermissionsCollections[] $relatedUsersToPermissionsCollections Связь к промежуточной таблице наборов пользовательских доступов
 * @property Permissions[] $relatedPermissions Назначенные напрямую доступы
 * @property PermissionsCollections[] $relatedPermissionsCollections Назначенные группы разрешений
 */
trait UsersPermissionsTrait {

	/**
	 * Проверяет, имеет ли пользователь указанный набор прав с указанной логикой проверки.
	 * Примеры:
	 * $user->hasPermission(['execute_order_66'])
	 * $user->hasPermission(['rule_galaxy', 'lose_arm'], Permissions::LOGIC_AND)
	 *
	 * @param string[] $permissions Названия прав, к которым проверяются доступы
	 * @param int $logic Логика проверки
	 * @return bool
	 * @throws Throwable
	 */
	public function hasPermission(array $permissions, int $logic = Permissions::LOGIC_OR):bool {
		$cacheKey = CacheHelper::MethodSignature(__METHOD__, func_get_args(), ['id' => $this->id]);
		return Yii::$app->cache->getOrSet($cacheKey, function() use ($permissions, $logic) {
			$result = false;
			$allUserPermissionsNames = ArrayHelper::getColumn(self::allPermissions(), 'name');
			foreach ($permissions as $current_permission_name) {
				if (false === $result = $this->isAllPermissionsGranted()) {
					$result = in_array(trim($current_permission_name), $allUserPermissionsNames, true);
				}

				switch ($logic) {
					case Permissions::LOGIC_OR:
						if ($result) return true; //при первом же найденном совпадении рапортуем о удаче
					break;
					case Permissions::LOGIC_AND:
						if (!$result) return false;//при первом же не найденном совпадении рапортуем о неудаче
					break;
					case Permissions::LOGIC_NOT:
						if ($result) return false;//при первом же найденном совпадении рапортуем о неудаче
					break;
				}
			}
			return ($logic === Permissions::LOGIC_NOT)?true:$result;
		}, null, new TagDependency(['tags' => CacheHelper::MethodSignature('Users::hasPermission', ['id' => $this->id])]));//тег ставится на все варианты запроса ролей пользователя для сброса скопом

	}

	/**
	 * Все доступы пользователя, отсортированные по приоритету от большего к меньшему
	 * Учитываются доступы групп пользователя + прямые доступы, без разделения
	 * @param bool $force false (default): получить кешированный набор прав; true: получить актуальный набор прав с обновлением кеша
	 * @return self[]
	 * @throws Throwable
	 */
	public function allPermissions(bool $force = false):array {
		$cacheKey = CacheHelper::MethodSignature('Users::allPermissions', ['id' => $this->id]);
		if ($force) Yii::$app->cache->delete($cacheKey);
		return Yii::$app->cache->getOrSet($cacheKey, function() {
			return array_merge(Permissions::allUserPermissions($this->id), Permissions::allUserConfigurationPermissions($this->id));
		}, null, new TagDependency(['tags' => $cacheKey]));
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedUsersToPermissions():ActiveQuery {
		return $this->hasMany(RelUsersToPermissions::class, ['user_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedPermissions():ActiveQuery {
		return $this->hasMany(Permissions::class, ['id' => 'permission_id'])->via('relatedUsersToPermissions');
	}

	/**
	 * @param mixed $relatedPermissions
	 * @throws Throwable
	 */
	public function setRelatedPermissions($relatedPermissions):void {
		/** @var ActiveRecord $this */
		if (empty($relatedPermissions)) {
			RelUsersToPermissions::clearLinks($this);
		} else {
			RelUsersToPermissions::linkModels($this, $relatedPermissions);
		}
		TagDependency::invalidate(Yii::$app->cache, [CacheHelper::MethodSignature('Users::allPermissions', ['id' => $this->id])]);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedUsersToPermissionsCollections():ActiveQuery {
		return $this->hasMany(RelUsersToPermissionsCollections::class, ['user_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedPermissionsCollections():ActiveQuery {
		return $this->hasMany(PermissionsCollections::class, ['id' => 'collection_id'])->via('relatedUsersToPermissionsCollections');
	}

	/**
	 * @param mixed $relatedPermissionsCollections
	 * @throws Throwable
	 */
	public function setRelatedPermissionsCollections($relatedPermissionsCollections):void {
		/** @var ActiveRecord $this */
		if (empty($relatedPermissionsCollections)) {
			RelUsersToPermissionsCollections::clearLinks($this);
		} else {
			RelUsersToPermissionsCollections::linkModels($this, $relatedPermissionsCollections);
		}
		TagDependency::invalidate(Yii::$app->cache, [CacheHelper::MethodSignature('Users::allPermissions', ['id' => $this->id])]);
	}

	/**
	 * Есть ли у пользователя доступ к экшену
	 * @param Action $action
	 * @return bool
	 * @throws Throwable
	 */
	public function hasActionPermission(Action $action):bool {
		if ($this->isAllPermissionsGranted()) return true;
		return $this->hasControllerPermission($action->controller->id, $action->id, Yii::$app->request->method);
	}

	/**
	 * Проверка наличия разрешения на доступ к контроллеру, и, опционально, экшену с указанным методом
	 * @param string $controllerId
	 * @param string|null $actionId
	 * @param string|null $verb
	 * @return bool
	 * @throws Throwable
	 */
	public function hasControllerPermission(string $controllerId, ?string $actionId = null, ?string $verb = null):bool {
		if ($this->isAllPermissionsGranted()) return true;
		$cacheKey = CacheHelper::MethodSignature(__METHOD__, [
			'id' => $this->id,
			'controller' => $controllerId,
			'action' => $actionId,
			'verb' => $verb
		]);
		return Yii::$app->cache->getOrSet($cacheKey, function() use ($controllerId, $actionId, $verb) {
			return [] !== Permissions::allUserPermissions($this->id, [
					'controller' => $controllerId,
					'action' => $actionId,
					'verb' => $verb
				]) || [] !== Permissions::allUserConfigurationPermissions($this->id);
		}, null, new TagDependency([
			'tags' => [
				CacheHelper::MethodSignature('Users::allPermissions', ['id' => $this->id]),//сброс кеша при изменении прав пользователя
			]
		]));
	}

	/**
	 * Проверяет перегрузку доступов через конфиг
	 * @return bool
	 * @throws Throwable
	 */
	public function isAllPermissionsGranted():bool {
		return in_array($this->id, Permissions::ConfigurationParameter(Permissions::GRANT_ALL, []), true);
	}

}