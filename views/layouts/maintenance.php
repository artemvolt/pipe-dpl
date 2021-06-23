<?php
declare(strict_types = 1);

/**
 * Технические работы
 * @var View $this
 * @var string $content
 */

use app\assets\AppAsset;
use yii\bootstrap4\Html;
use yii\web\View;

AppAsset::register($this);
?>
<!DOCTYPE html>
<?php $this->beginPage() ?>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=0.4">
	<?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="maintenance bg-img"></div>
<?php $this->endBody() ?>
</body>
<?php $this->endPage() ?>
</html>