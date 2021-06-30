<?php
declare(strict_types = 1);

namespace app\modules\api\connectors\ivi;

/**
 * Class PurchaseItem
 * @package app\modules\api\connectors\ivi
 */
class PurchaseItem {
	private array $_data;

	public function __construct(array $data) {
		$this->_data = $data;
	}
}