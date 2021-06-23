<?php
declare(strict_types = 1);

namespace app\widgets\selectmodelwidget;

use pozitronik\sys_options\models\SysOptions;
use yii\web\AssetBundle;

/**
 * Class SelectModelWidgetAssets
 */
class SelectModelWidgetAssets extends AssetBundle {
	/**
	 * @inheritdoc
	 */
	public function init():void {
		$this->sourcePath = __DIR__.'/assets';
//		$this->css = ['css/select_model.css'];
		$this->js = [
			'js/select_model.js'
		];
		$this->publishOptions = [
			'forceCopy' => SysOptions::getStatic('ASSETS_PUBLISHOPTIONS_FORCECOPY', false)
		];
		parent::init();
	}
}