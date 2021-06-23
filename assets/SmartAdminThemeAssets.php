<?php
declare(strict_types = 1);

namespace app\assets;

use pozitronik\sys_options\models\SysOptions;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Class SmartAdminThemeWidgetAssets
 */
class SmartAdminThemeAssets extends AssetBundle {

	/**
	 * @inheritDoc
	 */
	public function init():void {
		$this->depends = [YiiAsset::class];
		$this->sourcePath = __DIR__.'/assets/theme';
		$this->css = [
			'css/vendors.bundle.css',
			'css/app.bundle.css',
			'css/fa-solid.css',
			'css/skins/skin-master.css',
			'css/notifications/toastr/toastr.css'
		];
		$this->js = [
			'js/vendors.bundle.js',
			'js/app.bundle.js',
			'js/notifications/toastr/toastr.js',
			'js/init.js'
		];
		$this->publishOptions = [
			'forceCopy' => SysOptions::getStatic('ASSETS_PUBLISHOPTIONS_FORCECOPY', false)
		];

		parent::init();
	}
}