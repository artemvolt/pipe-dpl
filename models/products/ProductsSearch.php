<?php
declare(strict_types = 1);

namespace app\models\products;

use app\models\products\active_record\ProductsAR;
use app\models\products\active_record\ProductsClassesAR;
use yii\data\ActiveDataProvider;
use DomainException;

/**
 * Class ProductSearch
 * todo
 */
class ProductsSearch extends ProductsClassesAR {

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search(array $params):ActiveDataProvider {
		$query = ProductsAR::find()->active();

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		$dataProvider->setSort([
			'defaultOrder' => ['id' => SORT_ASC],
			'attributes' => [
			]
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		return $dataProvider;
	}

	/**
	 * @param int $orderId
	 * @return ProductOrder
	 */
	public function getExistentSimcardOrder(int $orderId):ProductOrder {
		if ($find = ProductOrder::findOne(['id' => $orderId])) {
			return $find;
		}
		throw new DomainException("Не получилось найти заказ по симкарте с id $orderId");
	}

}
