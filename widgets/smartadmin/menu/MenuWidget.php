<?php
declare(strict_types = 1);

namespace app\widgets\smartadmin\menu;

use Yii;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Html;
use yii\widgets\Menu as YiiMenuWidget;

/**
 * Class MenuWidget
 * @package app\widgets\smartadmin\menu
 *
 * Виджет для отрисовки меню
 */
class MenuWidget extends YiiMenuWidget {
	private bool $_activeItemFound = false;

	/**
	 * @inheritdoc
	 */
	public function init():void {
		parent::init();

		$this->linkTemplate = '<a href="{url}" data-filter-tags="{tags}">{icon}<span class="nav-link-text">{label}</span></a>';
	}

	/**
	 * @inheritdoc
	 */
	protected function isItemActive($item):bool {
		//если нашли активный элемент меню, к чему дальнейшие поиски?!
		if ($this->_activeItemFound) {
			return false;
		}

		if (Yii::$app->controller && (null !== $route = ArrayHelper::getValue($item, 'url.0'))) {
			//дополнительная проверка в связи с возможностью передачи Url без указания экшона (по-умолчанию)
			$syntheticRoute = trim(Yii::getAlias($route), '/').'/'.Yii::$app->controller->defaultAction;
			if ($syntheticRoute === Yii::$app->controller->route) {
				$item['url'][0] = '/'.$syntheticRoute;
			}
		}

		return $this->_activeItemFound = parent::isItemActive($item);
	}

	/**
	 * @inheritdoc
	 */
	protected function renderItem($item):string {
		$render = parent::renderItem($item);

		$icon = (null === $itemClass = ArrayHelper::getValue($item, 'iconClass'))?'':Html::tag('i', '', ['class' => "fal {$itemClass}"]);

		$tags = mb_strtolower($item['label']);

		return strtr($render, ['{icon}' => $icon, '{tags}' => $tags]);
	}
}