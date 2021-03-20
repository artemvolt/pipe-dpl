<?php
declare(strict_types = 1);
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use pozitronik\sys_options\models\SysOptions;
use yii\web\AssetBundle;
use yii\web\YiiAsset;
use yii\bootstrap\BootstrapAsset;

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
			'css/site.css'
		];

		$this->depends = [
			YiiAsset::class,
			BootstrapAsset::class,
		];

		$this->publishOptions = [
			'forceCopy' => SysOptions::getStatic('assets.publishOptions.forceCopy', false)
		];
		parent::init();
	}

}
