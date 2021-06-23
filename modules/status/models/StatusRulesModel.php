<?php
declare(strict_types = 1);

namespace app\modules\status\models;

use app\modules\status\StatusModule;
use pozitronik\helpers\ArrayHelper;
use Throwable;
use yii\base\Model;

/**
 * Прототипирование набора правил статусов для цели
 */
class StatusRulesModel extends Model {
	/**
	 * Ищет в массиве подмассив (простую пару ключ-значение), возвращает путь до найденного результата
	 * Не отлаживалось - функция нужна только в прототипе для поиска в self::RULES
	 * @param array $array
	 * @param array $search
	 * @param array $keys
	 * @return array
	 * @throws Throwable
	 */
	private static function array_find_subarray(array $array, array $search, array $keys = []):array {
		$searchKey = ArrayHelper::key($search);
		/** @var string $searchKey */
		$searchValue = $search[$searchKey];
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				if ($searchValue === ArrayHelper::getValue($value, $searchKey)) {
					return array_merge($keys, [$key]);
				}

				$sub = self::array_find_subarray($value, $search, array_merge($keys, [$key]));
				if (count($sub)) {
					return $sub;
				}
			}
		}
		return [];
	}

	/**
	 * @param string $className
	 * @return StatusModel|null
	 * @throws Throwable
	 */
	public static function getInitialStatus(string $className):?StatusModel {
		$ruleId = ArrayHelper::getValue(self::array_find_subarray(StatusModule::getClassRules($className), ['initial' => true]), 0);
		return self::getStatus($className, $ruleId);

	}

	/**
	 * @param string $className
	 * @param int|null $currentStatusId
	 * @return StatusModel|null
	 * @throws Throwable
	 */
	public static function getStatus(string $className, ?int $currentStatusId):?StatusModel {
		if (null === $rule = ArrayHelper::getValue(StatusModule::getClassRules($className), $currentStatusId)) return null;
		/** @var array $rule */
		return new StatusModel($currentStatusId, $rule);
	}

	/**
	 * @param string $className
	 * @param StatusModel|null $currentStatus
	 * @return null|array
	 * @throws Throwable
	 */
	public static function getNextStatuses(string $className, ?StatusModel $currentStatus):?array {
		if (null === $currentStatus) return [];
		if (null === $nextStatusesId = $currentStatus->next) return null;//может быть применён любой статус
		$nextStatuses = [];
		foreach ($nextStatusesId as $statusId) {
			$nextStatuses[] = self::getStatus($className, $statusId);
		}
		return $nextStatuses;
	}

	/**
	 * @param string $className
	 * @return StatusModel[]
	 * @throws Throwable
	 */
	public static function getAllStatuses(string $className):array {
		$rules = StatusModule::getClassRules($className);
		$allStatuses = [];
		foreach ($rules as $id => $rule) {
			$allStatuses[] = new StatusModel($id, $rule);
		}
		return $allStatuses;
	}
}

