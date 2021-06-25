<?php
declare(strict_types = 1);

namespace app\components\db;

use app\models\sys\users\Users;
use yii\db\ActiveQuery as YiiActiveQuery;
use yii\db\ActiveRecord;

/**
 * Trait ActiveQueryTrait
 * Каст расширения запросов
 */
class ActiveQuery extends YiiActiveQuery {

	/**
	 * Селектор для флага "deleted", если он присутствует в таблице
	 * @param bool $deleted
	 * @return $this
	 * @example ActiveRecord::find()->active()->all()
	 */
	public function active(bool $deleted = false):self {
		/** @var ActiveRecord $class */
		$class = new $this->modelClass();//Хак для определения вызывающего трейт класса (для определения имени связанной таблицы)
		$tableName = $class::tableName();
		return $class->hasAttribute('deleted')?$this->andOnCondition([$tableName.'.deleted' => $deleted]):$this;
	}

	/**
	 * Возвращает область видимости пользователя $user для модели $modelClass (если та реализует метод self::scope);
	 * @param string|object $modelObjectOrClass
	 * @param Users $user
	 * @return $this
	 */
	public function scope(string $modelObjectOrClass, Users $user):self {
		if (method_exists($modelObjectOrClass, 'scope')) {
			return ($modelObjectOrClass::scope($this, $user));
		}

		return $this;
	}
}