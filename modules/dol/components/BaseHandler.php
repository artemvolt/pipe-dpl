<?php
declare(strict_types = 1);

namespace app\modules\dol\components;

use RuntimeException;
use yii\httpclient\Response;

/**
 * Class Handler
 * @package app\modules\dol\components
 */
class BaseHandler {
	/**
	 * @param Response $response
	 * @return array
	 */
	public function handle(Response $response):array {
		if (null === $result = json_decode($response->content, true, 512, JSON_OBJECT_AS_ARRAY)) {
			throw new RuntimeException('Не получилось распознать ответ от сервера');
		}
		return $result;
	}
}