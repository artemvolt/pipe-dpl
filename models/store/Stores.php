<?php
declare(strict_types = 1);

namespace app\models\store;

use app\models\store\active_record\StoresAR;

/**
 * Class Stores
 * Точка продажи товаров (как организация): собственный офис, франчайзи, дилеры, etc.
 */
class Stores extends StoresAR {
	public const RUS_CLASS_NAME = 'Магазин';

	/**
	 * Заглушка, чтобы инспектор не ругался на пустой стаб
	 */
	public function dummy_todo_function():void {

	}
}