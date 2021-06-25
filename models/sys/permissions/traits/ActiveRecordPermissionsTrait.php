<?php
declare(strict_types = 1);

namespace app\models\sys\permissions\traits;

use yii\db\ActiveQueryInterface;
use yii\web\IdentityInterface;

/**
 * Trait ActiveRecordPermissionsTrait
 * Управление областями видимости в ActiveRecord
 */
trait ActiveRecordPermissionsTrait {

	/**
	 * Интерфейс функции установки области доступа пользователя в этой таблице
	 * @param ActiveQueryInterface $query
	 * @param IdentityInterface $user
	 * @return mixed
	 */
	abstract public static function scope(ActiveQueryInterface $query, IdentityInterface $user);

}