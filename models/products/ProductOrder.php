<?php
declare(strict_types = 1);

namespace app\models\products;

use app\models\products\active_record\ProductOrderAR;

/**
 * Class ProductOrder
 * Описание заказа
 */
class ProductOrder extends ProductOrderAR {
	/**
	 * @return bool
	 */
	public function isSimcard():bool {
		return true;
	}

	/**
	 * Заглушка, чтобы инспектор не ругался на пустой стаб
	 */
	public function dummy_todo_function():void {

	}
}