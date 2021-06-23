<?php
declare(strict_types = 1);

namespace app\models\core;

/**
 * Class Options
 * Перечисление системных опций, вынесенных в SysOptions
 */
class Options {
	/*значения по умолчанию*/
	public const ASSETS_PUBLISHOPTIONS_FORCECOPY = false;
	/**
	 * Формат: ключ => описание опции
	 */
	public const OPTIONS_LABELS = [
		'ASSETS_PUBLISHOPTIONS_FORCECOPY' => 'Отключить кеширование ассетов',
	];
}