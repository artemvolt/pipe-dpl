<?php
declare(strict_types = 1);

namespace app\modules\dol\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

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
	 */
	public function LoadFromResponseArray(array $responseArray) {
		$this->value = ArrayHelper::getValue($responseArray, 'authToken.value');
		$this->expires = ArrayHelper::getValue($responseArray, 'authToken.expires');
	}
}