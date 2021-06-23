<?php
declare(strict_types = 1);

namespace app\models\products;

use app\models\products\active_record\SimCardAR;
use app\models\reward\Rewards;
use app\models\sales\Sales;
use yii\base\ModelEvent;

/**
 * Class SimCard
 * Описание симки и всех её жизненных процессов.
 * Симка заказывается
 */
class SimCard extends SimCardAR implements ProductsInterface {
	public int $type = 1;

	public function doSell():?bool {
		$sale = Sales::register($this);
		Rewards::register($sale->rewards);
		$event = new ModelEvent();
		$this->trigger(self::EVENT_SELL, $event);
		return $event->isValid;
	}

	public function doConfirm():?bool {
		$event = new ModelEvent();
		$this->trigger(self::EVENT_CONFIRM, $event);
		return $event->isValid;
	}

}