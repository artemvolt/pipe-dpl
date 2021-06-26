<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\mutations;

/**
 * Interface MutationInterface
 * @package app\modules\graphql\schema\mutations
 */
interface MutationInterface {
	/**
	 * Указываем атрибутный состав для мутаций
	 * @return array
	 */
	public function getArgs():array;

	/**
	 * Сообщение об успешной/ошибочной операции сохранения.
	 * Используется в на фронте в popup message
	 * @return array
	 */
	public function getMessages():array;
}
