<?php
declare(strict_types = 1);

use app\assets\AppAsset;
use yii\bootstrap4\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $content
 */

AppAsset::register($this);
?>
<!DOCTYPE html>
<?php $this->beginPage(); ?>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?= Html::csrfMetaTags() ?>
	<title>Preview Email Template</title>
	<?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>
<div id="page-content">
	<div class="boxed">
		<div id="content-container">
			<?= $content ?>
		</div>
	</div>
</div>
<?php $this->endBody(); ?>
</body>
<?php $this->endPage(); ?>
</html>