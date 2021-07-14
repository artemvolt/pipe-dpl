<?php
declare(strict_types = 1);

namespace app\models\tests\product;

use app\models\products\ProductOrder;
use app\models\tests\core\prototypes\ActiveRecordTraitTest;

/**
 * Class ProductOrderTest
 * @package app\models\tests\product
 */
class ProductOrderTest extends ProductOrder {
	use ActiveRecordTraitTest;

	/**
	 * @param int $initiator
	 * @param int $store
	 * @param int $status
	 * @return static
	 */
	public static function create(int $initiator, int $store, int $status):self {
		$order = new self();
		$order->initiator = $initiator;
		$order->store = $store;
		$order->status = $status;
		$order->create_date = date('Y-m-d H:i:s');
		return $order;
	}
}