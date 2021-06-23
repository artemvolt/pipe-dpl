<?php
declare(strict_types = 1);

namespace app\widgets\smartadmin\sidebar;

use Exception;
use yii\base\Widget as YiiBaseWidget;
use yii\helpers\ArrayHelper;
use yii\widgets\Menu;

/**
 * Class SideBarWidget
 * @package app\widgets
 *
 * Виджет для отрисовки панели навигации
 */
class SideBarWidget extends YiiBaseWidget {
	/**
	 * @var array список элементов Меню
	 * @see Menu::$items
	 */
	public array $items = [];

	/**
	 * @inheritDoc
	 */
	public function run():string {
		$this->items = self::prepareItems($this->items);
		$this->items = self::clearItems($this->items);
		return $this->render('main', ['items' => $this->items]);
	}

	/**
	 * Удаляет все узлы, помеченные 'visible' => false
	 * @param array $items
	 * @return array
	 * @throws Exception
	 */
	private static function prepareItems(array $items):array {
		foreach ($items as &$item) {
			if ((null !== $subItems = ArrayHelper::getValue($item, 'items')) && is_array($subItems)) {
				$item['items'] = self::prepareItems($item['items']);
			}
			$item = ArrayHelper::getValue($item, 'visible', true)?$item:[];
		}

		return $items;
	}

	/**
	 * Удаляет все пустые узлы
	 * @param array $items
	 * @return array
	 * @throws Exception
	 */
	private static function clearItems(array $items):array {
		$items = array_filter($items);
		foreach ($items as $key => $item) {
			if ((null !== $subItems = ArrayHelper::getValue($item, 'items')) && is_array($subItems) && [] !== $subItems) {
				$item['items'] = self::clearItems($item['items']);
			}
			if ([] === ArrayHelper::getValue($item, 'items')) {
				unset($items[$key]);
			}
		}
		return $items;
	}
}