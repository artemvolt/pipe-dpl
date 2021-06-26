<?php
declare(strict_types = 1);

namespace app\components\db;

use app\models\sys\users\Users;
use pozitronik\traits\models\ActiveQuery as VendorActiveQuery;
use yii\web\ForbiddenHttpException;

/**
 * Trait ActiveQueryTrait
 * Каст расширения запросов
 */
class ActiveQuery extends VendorActiveQuery {

	/**
	 * Возвращает область видимости пользователя $user для модели $modelClass (если та реализует метод self::scope);
	 * @param string|object $modelObjectOrClass
	 * @param ?Users $user
	 * @return $this
	 * @throws ForbiddenHttpException
	 */
	public function scope(string $modelObjectOrClass, ?Users $user = null):self {
		if (method_exists($modelObjectOrClass, 'scope')) {
			$user = $user??Users::Current();
			return ($modelObjectOrClass::scope($this, $user));
		}

		return $this;
	}
}