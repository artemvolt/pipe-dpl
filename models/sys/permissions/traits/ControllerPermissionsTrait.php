<?php
declare(strict_types = 1);

namespace app\models\sys\permissions\traits;

use app\models\sys\users\Users;
use pozitronik\core\traits\ControllerTrait;
use Throwable;
use yii\web\NotFoundHttpException;

/**
 * Управление правами доступа для контроллера
 * Trait ControllerPermissionsTrait
 */
trait ControllerPermissionsTrait {
	use ControllerTrait;

	/**
	 * Проверяет, есть ли у пользователя доступ к контроллеру, и, опционально, экшену
	 * @param null|string $actionId ActionId, если не указан - то проверяется доступ ко всему контроллеру
	 * @param int|null $userId id пользователя, если не указан - текущий
	 * @return bool
	 * @throws Throwable
	 */
	public static function hasPermission(?string $actionId = null, ?int $userId = null):bool {
		if (null === ($user = null === $userId?Users::Current():Users::findOne($userId))) {
			throw new NotFoundHttpException();
		}

		return $user->hasControllerPermission(self::ExtractControllerId(static::class), $actionId);
	}

}