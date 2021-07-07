<?php
declare(strict_types = 1);

namespace app\modules\dol\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use Exception;

/**
 * Class DolAuthToken
 * Простенькая модель хранения/обновления токена DOL
 */
class DolAuthToken extends Model {
	public ?string $value = null;
	public ?string $expires = null;

	/**
	 * @return string
	 */
	public function __toString():string {
		return $this->value;
	}

	/**
	 * @param array $responseArray
	 * @throws Exception
	 */
	public function loadFromResponseArray(array $responseArray):void {
		$this->value = ArrayHelper::getValue($responseArray, 'authToken.value');
		$this->expires = ArrayHelper::getValue($responseArray, 'authToken.expires');
	}
}