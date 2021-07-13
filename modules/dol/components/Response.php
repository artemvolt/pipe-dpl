<?php
declare(strict_types = 1);

namespace app\modules\dol\components;

use yii\helpers\Json;

/**
 * Class Response
 * @package app\modules\dol\components
 */
abstract class Response {
	protected array $data;

	public function __construct(array $data) {
		$this->data = $data;
	}

	/**
	 * @param string $json
	 * @return array
	 */
	public static function fromJsonString(string $json):array {
		$class = static::class;
		return new $class(Json::decode($json));
	}
}