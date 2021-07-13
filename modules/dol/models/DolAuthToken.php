<?php
declare(strict_types = 1);

namespace app\modules\dol\models;

use DateTimeImmutable;
use yii\base\Model;

/**
 * Class DolAuthToken
 * Простенькая модель хранения/обновления токена DOL
 */
class DolAuthToken extends Model {
	public ?string $value = null;
	public ?DateTimeImmutable $expires = null;

	/**
	 * @param string $value
	 * @param DateTimeImmutable $expires
	 * @return DolAuthToken
	 */
	public static function create(string $value, DateTimeImmutable $expires):DolAuthToken {
		$self = new self();
		$self->value = $value;
		$self->expires = $expires;
		return $self;
	}

	/**
	 * @return string
	 */
	public function __toString():string {
		return $this->value;
	}
}