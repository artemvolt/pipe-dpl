<?php
declare(strict_types = 1);

namespace app\modules\api\connectors\ivi;

use yii\helpers\ArrayHelper;

/**
 * Класс для инкапсуляции логики взаимодействия с параметрами опций, полученными от ivi.
 * Class PurchasesOptionsHandler
 * @package app\modules\api\connectors\ivi
 */
class PurchaseOptionsHandler {
	private array $_data;
	/**
	 * @var PurchaseCollection коллекция подключенных подписок по абоненту.
	 */
	private PurchaseCollection $_purchasesCollection;
	/**
	 * @var PurchaseOptionsCollection коллекция опций подписок для создания/обновления подписки.
	 */
	private PurchaseOptionsCollection $_purchasesOptionsCollection;

	/**
	 * PurchasesOptions constructor.
	 * @param array $data
	 */
	public function __construct(array $data) {
		$this->_data = $data;

		$this->_purchasesCollection = new PurchaseCollection();
		$this->_purchasesOptionsCollection = new PurchaseOptionsCollection();

		$this->configurePurchasesCollection();
		$this->configurePurchasesOptionsCollection();
	}

	/**
	 * @return PurchaseCollection
	 */
	public function getPurchasesCollection():PurchaseCollection {
		return $this->_purchasesCollection;
	}

	/**
	 * @return PurchaseOptionsCollection
	 */
	public function getPurchasesOptionsCollection():PurchaseOptionsCollection {
		return $this->_purchasesOptionsCollection;
	}

	private function configurePurchasesCollection():void {
		$data = ArrayHelper::remove($this->_data, 'purchases', []);
		foreach ($data as $itemData) {
			$this->_purchasesCollection->addPurchase(new PurchaseItem($itemData));
		}
	}

	private function configurePurchasesOptionsCollection():void {
		$data = ArrayHelper::remove($this->_data, 'purchase_options', []);
		foreach ($data as $itemData) {
			$this->_purchasesOptionsCollection->addPurchaseOptionsItem(new PurchaseOptionsItem($itemData));
		}
	}
}