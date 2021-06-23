<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var array $items
 */

use yii\web\View;

?>
<aside class="page-sidebar">
	<?= $this->render('subviews/logo') ?>
	<nav id="js-primary-nav" class="primary-nav js-list-filter" role="navigation"
		 style="overflow: hidden; width: auto; height: 100%;">
		<?= $this->render('subviews/nav-filter') ?>
		<?= $this->render('subviews/info-card') ?>
		<?= $this->render('subviews/menu', compact('items')) ?>
	</nav>
</aside>
