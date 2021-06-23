<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var string $content
 */

use app\assets\LoginAsset;
use yii\bootstrap4\Html;
use yii\web\View;

LoginAsset::register($this);
?>
<!DOCTYPE html>
<?php $this->beginPage(); ?>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<title><?= $this->title ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="msapplication-tap-highlight" content="no">
	<?= Html::csrfMetaTags() ?>
	<?php $this->head(); ?>
</head>

<body class="mod-bg-1 mod-nav-link mod-skin-light">
<?php $this->beginBody(); ?>
<div class="page-wrapper auth">
	<div class="page-inner bg-brand-gradient">
		<div class="page-content-wrapper bg-transparent m-0">
			<div class="height-10 w-100 shadow-lg px-4 bg-brand-gradient">
				<div class="d-flex align-items-center container p-0">
					<div
						class="page-logo width-mobile-auto m-0 align-items-center justify-content-center p-0 bg-transparent bg-img-none shadow-0 height-9 border-0">
						<a href="javascript:void(0)" class="page-logo-link press-scale-down d-flex align-items-center">
							<img src="/img/theme/logo-bee.png" alt="<?= Yii::$app->name ?>" aria-roledescription="logo">
							<span class="page-logo-text mr-1"><?= Yii::$app->name ?></span>
						</a>
					</div>
				</div>
			</div>
			<div class="flex-1"
				 style="background: url(/img/theme/svg/pattern-1.svg) no-repeat center bottom fixed; background-size: cover;">
				<div class="container py-4 py-lg-5 my-lg-5 px-4 px-sm-0">
					<?= $content ?>
					<div class="position-absolute pos-bottom pos-left pos-right p-3 text-center">
						<?= date('Y').' © '.Yii::$app->name ?>
					</div>
				</div>
			</div>
			<?= $this->render('subviews/js-color-profile') ?>
		</div>
	</div>
</div>
<?php $this->endBody(); ?>
</body>
<?php $this->endPage(); ?>
</html>