<?php
declare(strict_types = 1);

namespace app\modules\api\connectors\ivi;

use app\components\CollectionInterface;

/**
 * Коллекция опций подписок для создания/обновления подписки.
 * Class PurchasesOptionsCollection
 * @package app\modules\api\connectors\ivi
 */
class PurchaseOptionsCollection implements CollectionInterface {
	/**
	 * @var PurchaseOptionsItem[]
	 */
	private array $_items = [];

	/**
	 * @param PurchaseOptionsItem $item
	 */
	public function addPurchaseOptionsItem(PurchaseOptionsItem $item):void {
		$this->_items[] = $item;
	}

	/**
	 * Получение объекта опций по заданному продукту.
	 * @param string $productId идентификатор продукта в системе ivi.
	 * @return PurchaseOptionsItem|null
	 */
	public function extractProductPurchaseOptions(string $productId):?PurchaseOptionsItem {
		$items = array_filter(
			$this->_items,
			static function(PurchaseOptionsItem $item) use ($productId) {
				return $productId === $item->getProductId();
			});

		return ([] !== $items)?array_shift($items):null;
	}

	/**
	 * @return PurchaseOptionsItem[]
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