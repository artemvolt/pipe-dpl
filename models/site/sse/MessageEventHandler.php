<?php
declare(strict_types = 1);

namespace app\models\site\sse;

use odannyc\Yii2SSE\SSEBase;

/**
 * Class MessageEventHandler
 */
class MessageEventHandler extends SSEBase {

	public static int $i = 0;

	/**
	 * @inheritDoc
	 */
	public function check():bool {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function update():string {
		return "Update ".self::$i++;
	}
}