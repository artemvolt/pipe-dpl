<?php
declare(strict_types = 1);

namespace app\models\products;

/**
 * Interface MerchInterface
 * Описывает любой товар, передаваемый в продажу
 *
 * @property int $id
 * @property int $type уникальный id типа продукта
 */
interface ProductsInterface {
	public const EVENT_CONFIRM = 'Confirm';
	public const EVENT_SELL = 'Sell';

	/**
	 * Регистрация продажи товара
	 * @return null|bool Успешность операции, null - отсутствие подтверждения.
	 */
	public function doSell():?bool;

}