<?php
declare(strict_types = 1);

namespace app\widgets\smartadmin\cropper;

use app\assets\SmartAdminThemeAssets;
use pozitronik\sys_options\models\SysOptions;
use yii\web\AssetBundle as YiiAssetBundle;

/**
 * Class CropperWidgetAsset
 * @package app\widgets\smartadmin\cropper\assets
 */
class CropperWidgetAsset extends YiiAssetBundle {
	public function init():void {
		$this->depends = [
			SmartAdminThemeAssets::class
		];
		$this->sourcePath = __DIR__.'/assets';
		$this->css = [
			'css/cropper.css',
			'css/cropper-custom.css',
		];
		$this->js = [
			'js/cropper.js',
			'js/cropper-actions.js',
		];
		$this->publishOptions = [
			'forceCopy' => SysOptions::getStatic('ASSETS_PUBLISHOPTIONS_FORCECOPY', false)
		];

		parent::init();
	}
}