<?php
declare(strict_types = 1);

namespace app\models\products;

use app\models\products\active_record\ProductsAR;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class Products
 * @property-read int $id
 * @property-read string $name
 * @property-read string $class
 */
class Products extends ProductsAR {
	public ?int $id = null;
	public ?string $name = null;
	public ?string $class = null;

	/**
	 * @return self[]
	 * @throws Exception
	 */
	public static function all():array {
		$config = ArrayHelper::getValue(Yii::$app, 'params.productsConfig', []);
		$result = [];
		foreach ($config as $id => $attributes) {
			$result[] = new self([
				'id' => $id,
				'name' => ArrayHelper::getValue($attributes, 'name', new InvalidConfigException('name parameter required')),
				'class' => ArrayHelper::getValue($attributes, 'class', new InvalidConfigException('class parameter required')),
			]);
		}
		return $result;
	}

	/**
	 * @param int $modelId
	 * @param int $productType
	 * @return ProductsInterface|null
	 * @throws Exception
	 */
	public static function getModel(int $modelId, int $productType):?ProductsInterface {
		if (null === $modelClass = ArrayHelper::getValue(Yii::$app, "params.productsConfig.{$productType}.class")) return null;
		return $modelClass::findOne($modelId);
	}

}