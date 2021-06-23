<?php
declare(strict_types = 1);

namespace app\models\reward\config;

use app\models\products\ProductsInterface;
use app\models\reward\Rewards;
use Exception;
use pozitronik\references\models\ArrayReference;
use yii\helpers\ArrayHelper;

/**
 * Class RewardsRulesConfig
 * Временная затычка на месте конфигуратора правил вознаграждений. Пока что задаём все правила массивом.
 *
 * @property-read int $reason
 * @property-read int $status
 * @property-read int $quantity
 * @property-read null|string $waiting
 */
class RewardsRulesConfig extends ArrayReference {
	public string $menuCaption = 'Конфигурация правил вознаграждений';

	public int $reason;
	public int $status;
	public int $quantity;
	public ?string $waiting;

	/*Прототипирую правила*/
	private const RULES = [
		/*product_id => [[rule1],[rule2]] */
		1 => [//simcard
			[
				'reason' => Rewards::REASON_SALE_REGISTERED,
				'status' => Rewards::STATUS_APPLY,
				'quantity' => 50,
				'waiting' => null
			],
			[
				'reason' => Rewards::REASON_SALE_CONFIRMED,
				'status' => Rewards::STATUS_WAIT,
				'quantity' => 50,
				'waiting' => ProductsInterface::EVENT_CONFIRM
			],
		]
	];

	/**
	 * Пока вместо нормального генератора правил используем такую заглушку
	 * @param ProductsInterface $product
	 * @return self[]
	 * @throws Exception
	 */
	public static function findRules(ProductsInterface $product):array {
		$rules = ArrayHelper::getValue(self::RULES, $product->type);
		$result = [];
		foreach ($rules as $rule) {
			$result[] = new self($rule);
		}
		return $result;
	}
}