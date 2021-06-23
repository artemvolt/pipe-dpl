<?php
declare(strict_types = 1);

/**
 * @var array $items
 */

use app\widgets\smartadmin\menu\MenuWidget;

?>

<?= MenuWidget::widget([
	'options' => [
		'id' => 'js-nav-menu',
		'class' => 'nav-menu'
	],
	'activateParents' => true,
	'items' => $items,
]) ?>