<?php
declare(strict_types = 1);

namespace app\widgets\search;

use pozitronik\sys_options\models\SysOptions;
use yii\web\AssetBundle;
use app\assets\AppAsset;

/**
 * Class SearchWidgetAssets
 * @package app\components\search
 */
class SearchWidgetAssets extends AssetBundle {
	/**
	 * @inheritdoc
	 */
	public function init():void {
		$this->depends = [AppAsset::class];
		$this->sourcePath = __DIR__.'/assets';
		$this->css = ['css/search.css'];
//		$this->js = ['js/search.js'];
		$this->publishOptions = [
			'forceCopy' => SysOptions::getStatic('ASSETS_PUBLISHOPTIONS_FORCECOPY', false)
		];
		parent::init();
	}
}