<?php
declare(strict_types = 1);

namespace app\modules\api\connectors\ivi;

use yii\helpers\ArrayHelper;

/**
 * Class PurchaseOptionsItem
 * @package app\modules\api\connectors\ivi
 */
class PurchaseOptionsItem {
	private array $_data;

	public function __construct(array $data) {
		$this->_data = $data;
	}

	public function getProductId():?string {
		return ArrayHelper::getValue($this->_data, 'product_identifier');
	}

	public function getPurchaseParams():array {
		$params = ArrayHelper::getValue($this->_data, 'payment_options.0.purchase_params', []);
		return array_filter($params, static function(string $name) {
			return '_' === $name[0];
		}, ARRAY_FILTER_USE_KEY);
	}

	public function getSignParam():?string {
		return ArrayHelper::getValue($this->_data, 'payment_options.0.purchase_params.sign');
	}
}