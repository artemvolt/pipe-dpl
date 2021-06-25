<?php
declare(strict_types = 1);

namespace app\models\sales;

use app\components\db\ActiveRecordTrait;
use app\models\products\Products;
use app\models\products\ProductsInterface;
use app\models\reward\config\RewardsOperationsConfig;
use app\models\reward\config\RewardsRulesConfig;
use app\models\reward\Rewards;
use app\models\sales\active_record\SalesAR;
use app\models\sys\users\Users;
use Exception;
use yii\web\ForbiddenHttpException;

/**
 * Class Sales prototype
 *
 * @property-read Rewards[] $rewards Вознаграждения, начисляемые за продажу товара
 * @property null|ProductsInterface $relatedProducts Связанный проданный продукта
 */
class Sales extends SalesAR {
	use ActiveRecordTrait;

	public const STATUS_REGISTERED = 0;

	/**
	 * @param ProductsInterface $product
	 * @param Users|null $user
	 * @return static
	 * @throws Exception
	 * @throws ForbiddenHttpException
	 */
	public static function register(ProductsInterface $product, ?Users $user = null):self {
		$user = $user??Users::Current();
		$sale = self::findForProduct($product);
		if (!$sale->isNewRecord) return $sale;//already registered, but ok while prototype
		$sale->relatedSeller = $user;
		$sale->status = self::STATUS_REGISTERED;
		if ($sale->save()) return $sale;
		/*todo: log errors*/
		throw new Exception("Cannot register sale");
	}

	/**
	 * @return Rewards[]
	 * @throws Exception
	 */
	public function getRewards():array {
		$rules = RewardsRulesConfig::findRules($this->relatedProducts);
		$rewards = [];
		foreach ($rules as $rule) {
			$rewards[] = new Rewards([
				'relatedProducts' => $this->relatedProducts,//товар, за который начисляется бонус
				'rule' => null,//временная фигня, пока правила у нас накиданы хардкодом
				'operation' => RewardsOperationsConfig::OPERATION_SELL,//инициирующая операция
				'reason' => $rule->reason,//причина начисления
				'status' => $rule->status,//статус
				'quantity' => $rule->quantity,
				'waiting' => $rule->waiting,
				'relatedUser' => $this->seller
			]);
		}
		return $rewards;
	}

	/**
	 * @return ProductsInterface|null
	 * @throws Exception
	 */
	public function getRelatedProducts():?ProductsInterface {
		return Products::getModel($this->product_id, $this->product_type);
	}

	/**
	 * Ну, допустим
	 * @param ProductsInterface $product
	 */
	public function setRelatedProducts(ProductsInterface $product):void {
		$this->product_id = $product->id;
		$this->product_type = $product->type;
	}
}