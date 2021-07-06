<?php
declare(strict_types = 1);

namespace app\components\exceptions;

use Throwable;
use Yii;

/**
 * Class MuteManager
 * @package app\components\exceptions
 */
class MuteManager {
	/**
	 * @param callable $function
	 * @return mixed
	 * @throws Throwable
	 * @throws Throwable
	 */
	public function mute(callable $function) {
		try {
			return $function();
		} catch (Throwable $e) {
			if (YII_DEBUG || YII_ENV_TEST) {
				throw $e;
			}
			Yii::error($e);
			return null;
		}
	}
}