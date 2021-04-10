<?php
declare(strict_types = 1);

namespace app\models\sys\permissions;

use app\models\core\CacheHelper;
use app\models\sys\permissions\active_record\Permissions as ActiveRecordPermissions;
use pozitronik\helpers\ArrayHelper;
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

	/*
	 * Пути к расположениям контроллеров, для подсказок в выбиралках.
	 * Формат:
	 * 	алиас каталога => префикс id
	 * Так проще и быстрее, чем пытаться вычислять префикс из контроллера (в нём id появляется только в момент вызова,
	 * и зависит от множества настроек), учитывая, что это нужно только в админке, и только в выбиралке.
	 */
	public const CONTROLLER_DIRS = [
		'@app/controllers' => '',
		'@app/controllers/api' => 'api'
	];

	/**
	 * @param int $user_id
	 * @param string[] $permissionFilters
	 * @return self[]
	 */
	public static function allUserPermissions(int $user_id, array $permissionFilters = []):array {
		$query = self::find()
			->distinct()
			->joinWith(['relatedUsersToPermissions directPermissions', 'relatedUsersToPermissionsCollections collectionPermissions'], false)
			->where(['directPermissions.user_id' => $user_id])
			->orWhere(['collectionPermissions.user_id' => $user_id])
			->orderBy([
				'priority' => SORT_DESC,
				'id' => SORT_ASC]);
		foreach ($permissionFilters as $paramName => $paramValue) {
			$paramValues = [$paramValue];
			/*для перечисленных параметров пустое значение приравнивается к любому*/
			if (in_array($paramName, self::ALLOWED_EMPTY_PARAMS, true)) {
				$paramValues[] = null;
			}
			$query->andWhere([self::tableName().".".$paramName => $paramValues]);

		}
		return $query->all();
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