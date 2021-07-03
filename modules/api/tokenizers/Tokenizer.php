<?php
declare(strict_types = 1);

namespace app\modules\api\tokenizers;

/**
 * Interface Tokenizer
 * @package app\modules\api\tokenizers
 */
interface Tokenizer {
	/**
	 * @return string ключ для доступа к интерфейсу АПИ.
	 */
	public function getAuthToken():string;

	/**
	 * @return null|string ключ для обновления связки ключей.
	 */
	public function getRefreshToken():?string;

	/**
	 * @return int время жизни ключа доступа (в секундах).
	 */
	public function getExpiresIn():int;
}