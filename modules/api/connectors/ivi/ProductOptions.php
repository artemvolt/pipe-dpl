<?php
declare(strict_types = 1);

namespace app\modules\api\connectors\ivi;

use app\models\phones\Phones;
use pozitronik\helpers\ArrayHelper;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;

/**
 * Класс для инкапсуляции параметров, используемых для взаимодействия с API ivi.
 * Class ProductOptions
 * @package app\modules\api\connectors\ivi
 *
 * @property string|null $product
 * @property string|null $phone
 * @property string|null $transactionId
 * @property-read string|null $appVersion
 */
class ProductOptions extends BaseObject {
	/**
	 * @var string|null идентификатор продукта, выдаваемый партнером ivi.
	 */
	private ?string $_product;
	/**
	 * @var string|null идентификатор версии приложения, выдаваемый партнером ivi (уникальное значение для каждого продукта).
	 */
	private ?string $_appVersion;
	/**
	 * @var string|null телефон абонента.
	 */
	private ?string $_phone;
	/**
	 * @var string|null уникальный параметр транзакции при подключении опции в ivi (например, идентификатор из биллинга).
	 * Обязателен при выполнении запроса на подключение/обновление подписки.
	 * @see IviConnector::makePurchase()
	 */
	private ?string $_transactionId;

	/**
	 * @return string|null
	 */
	public function getProduct():?string {
		return $this->_product;
	}

	/**
	 * @param int $productId системный идентификатор продукта.
	 * Конвертируется в идентификатор продукта на стороне ivi (совместно с appVersion).
	 * @throws InvalidConfigException|Throwable
	 */
	public function setProduct(int $productId):void {
		$this->_product = ArrayHelper::getValue(Yii::$app->params, "ivi.productMap.$productId.productId",
			new InvalidConfigException('Не заданы параметры продукта для ivi'));
		$this->_appVersion = ArrayHelper::getValue(Yii::$app->params, "ivi.productMap.$productId.appVersion",
			new InvalidConfigException('Не заданы параметры продукта для ivi'));
	}

	/**
	 * @return string|null
	 */
	public function getAppVersion():?string {
		return $this->_appVersion;
	}

	/**
	 * @return string|null
	 */
	public function getPhone():?string {
		return $this->_phone;
	}

	/**
	 * @param string $phone
	 */
	public function setPhone(string $phone):void {
		$this->_phone = '7'.Phones::defaultFormat($phone);
	}

	/**
	 * @return string|null
	 */
	public function getTransactionId():?string {
		return $this->_transactionId;
	}

	/**
	 * @param string $transactionId
	 */
	public function setTransactionId(string $transactionId):void {
		$this->_transactionId = $transactionId;
	}
}