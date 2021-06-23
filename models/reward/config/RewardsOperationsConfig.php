<?php
declare(strict_types = 1);

namespace app\models\reward\config;

use pozitronik\references\models\ArrayReference;

/**
 * Class RewardsOperationsConfig
 */
class RewardsOperationsConfig extends ArrayReference {
	public string $menuCaption = 'Конфигурация операций вознаграждений';

	public const OPERATION_SELL = 1;
	public const ACHIEVEMENT = 2;

	public array $items = [
		self::OPERATION_SELL => [
			'name' => 'Продажа товара'
		],
		self::ACHIEVEMENT => [
			'name' => 'Выполнение задания'
		]
	];

}