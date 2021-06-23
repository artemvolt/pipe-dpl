<?php
declare(strict_types = 1);
use app\models\sys\users\Users;
use pozitronik\widgets\BadgeWidget;
use yii\bootstrap4\BootstrapAsset;

BootstrapAsset::register($this);
?>

<?= BadgeWidget::widget([
	'items' => Users::find()->all(),
	'subItem' => 'username',
	'itemsSeparator' => ', ',
//	'innerPrefix' => fn($key, $users) => "{$key}-",
//	'innerPostfix' => fn($key) => ":{$key}",
//	'outerPrefix' => fn($key) => "{$key}?",
//	'outerPostfix' => fn($key) => "#{$key}",
	'emptyText' => ['один', 'два', 'три'],
	'visible' => 2,
	'options' => static function($key) {
		return ['style' => 'background:'.((0 === $key % 2)?'red':'green')];
	},
	'addon' => static fn($visible, $hidden) => "{$hidden} не показано",
	'addonOptions' => static function($key) {
		return ['style' => 'background:'.((0 === $key % 2)?'red':'green')];
	},
//	'urlScheme' => ['users/view', 'id' => 'id'],
	'tooltip' => static function($key, $users) {
		return (string)$key;
	},
	'addonTooltip' => static function(array $all, array $visible, array $hidden) {
		return implode(',', $hidden);
	}
]) ?>
