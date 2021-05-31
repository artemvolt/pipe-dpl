<?php
declare(strict_types = 1);

namespace app\modules\import;

use pozitronik\sys_options\models\SysOptions;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Class ImportModuleAssets
 */
class ImportModuleAssets extends AssetBundle {

	/**
	 * @inheritDoc
	 */
	public function init():void {
		$this->depends = [YiiAsset::class];
		$this->sourcePath = __DIR__.'/assets';
		$this->js = [
			'js/import.js',
		];
		$this->publishOptions = [
			'forceCopy' => SysOptions::getStatic('ASSETS_PUBLISHOPTIONS_FORCECOPY', false)
		];

		parent::init();
	}
}