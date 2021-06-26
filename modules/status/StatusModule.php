<?php
declare(strict_types = 1);

namespace app\modules\status;

use pozitronik\helpers\ArrayHelper;
use pozitronik\traits\traits\ModuleTrait;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;

/**
 * Class StatusModule
 * @package status
 */
class StatusModule extends Module {
	use ModuleTrait;

	/**
	 * @param string $className
	 * @return array
	 * @throws Throwable
	 */
	public static function getClassRules(string $className):array {
		$rules = ArrayHelper::getValue(Yii::$app->modules, "statuses.params.rules.$className", []);
		if (is_string($rules)) return $rules();

		if (!is_array($rules)) throw new InvalidConfigException("Настройки статусов класса $className заданы некорректно");
		return $rules;
	}
}