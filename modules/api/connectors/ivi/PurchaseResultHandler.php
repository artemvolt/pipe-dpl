<?php
declare(strict_types = 1);

namespace app\modules\api\connectors\ivi;

use Exception;
use yii\helpers\ArrayHelper;

/**
 * Class PurchaseResultHandler
 * @package app\modules\api\connectors\ivi
 */
class PurchaseResultHandler {
	public const STATUS_OK = 'ok';
	public const STATUS_PAYMENT_CONFIRMATION_REQUIRED = 'new';

	private array $_data;

	public function __construct(array $data) {
		$this->_data = $data;
	}

	public function isError():bool {
		return !in_array($this->getStatus(), [self::STATUS_OK, self::STATUS_PAYMENT_CONFIRMATION_REQUIRED], true);
	}

	/**
	 * ID покупки.
	 * @return int|null
	 * @throws Exception
	 */
	public function getPurchaseId():?int {
		return ArrayHelper::getValue($this->_data, 'purchase_id');
	}

	/**
	 * Статус покупки: `new` — покупка требует подтверждения оплаты, `ok` — покупка не требует подтверждения
	 * оплаты и можно начинать просмотр/предоставление услуги.
	 * Остальные можно воспринимать, как ошибку.
	 * @return string|null
	 * @throws Exception
	 */
	public function getStatus():?string {
		return ArrayHelper::getValue($this->_data, 'status');
	}

	/**
	 * ID начисления, по которому можно проверять состояние покупки.
	 * @return int|null
	 * @throws Exception
	 */
	public function getCreditId():?int {
		return ArrayHelper::getValue($this->_data, 'credit_id');
	}
}