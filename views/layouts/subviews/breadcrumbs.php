<?php
declare(strict_types = 1);

use yii\widgets\Breadcrumbs;

?>
<?= Breadcrumbs::widget([
	'tag' => 'ol',
	'options' => [
		'class' => 'breadcrumb page-breadcrumb'
	],
	'links' => $this->params['breadcrumbs']??[],
	'itemTemplate' => "<li class='breadcrumb-item'>{link}</li>\n",
	'activeItemTemplate' => "<li class='breadcrumb-item active'>{link}</li>\n"
]) ?>