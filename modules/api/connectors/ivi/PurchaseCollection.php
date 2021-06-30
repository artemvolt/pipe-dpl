<?php
declare(strict_types = 1);

namespace app\modules\api\connectors\ivi;

use app\components\CollectionInterface;

/**
 * Коллекция подключенных подписок по абоненту.
 * Class PurchasesCollection
 * @package app\modules\api\connectors\ivi
 */
class PurchaseCollection implements CollectionInterface {
	/**
	 * @var PurchaseItem[]
	 */
	private array $_items = [];

	/**
	 * @param PurchaseItem $item
	 */
	public function addPurchase(PurchaseItem $item):void {
		$this->_items[] = $item;
	}

	/**
	 * @return PurchaseItem[]
	 */
	public function getItems():array {
		return $this->_items;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isEmpty():bool {
		return [] === $this->_items;
	}
}