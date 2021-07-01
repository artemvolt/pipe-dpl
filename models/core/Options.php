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
	public const GRANT_TYPE_ISSUE_IGNORE_TOKEN_VALIDATION = false;
	/**
	 * Формат: ключ => описание опции
	 */
	public const OPTIONS_LABELS = [
		'ASSETS_PUBLISHOPTIONS_FORCECOPY' => 'Отключить кеширование ассетов',
		'GRANT_TYPE_ISSUE_IGNORE_TOKEN_VALIDATION' => 'Игнорировать валидацию токенов в GrantTypeIssue::validate()'
	];
}