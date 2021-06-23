<?php
declare(strict_types = 1);
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use pozitronik\sys_options\models\SysOptions;
use yii\bootstrap4\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Main application asset bundle.
 */
class AppAsset extends AssetBundle {

	/**
	 * {@inheritDoc}
	 */
	public function init():void {
		$this->sourcePath = __DIR__.'/assets/app/';
		$this->css = [
			'css/site.css',
			'css/navigation.css'
		];

		$this->depends = [
			YiiAsset::class,
			BootstrapAsset::class,
			SmartAdminThemeAssets::class
		];

		$this->publishOptions = [
			'forceCopy' => SysOptions::getStatic('ASSETS_PUBLISHOPTIONS_FORCECOPY', false)
		];
		parent::init();
	}

}
