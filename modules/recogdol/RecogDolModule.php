<?php
declare(strict_types = 1);

namespace app\modules\recogdol;

use Yii;
use Exception;
use yii\base\Module as YiiModule;
use yii\console\Application;

/**
 * Class Module
 * @package app\modules\recogdol
 */
class RecogDolModule extends YiiModule {

	/**
	 * @inheritDoc
	 */
	public function init():void {
		parent::init();

		try {
			if (Yii::$app instanceof Application) {
				$this->controllerNamespace = 'app\modules\recogdol\commands';
			}
		} catch (Exception $e) {
			Yii::error($e->getTraceAsString(), 'recogdol.api');
		}
	}
}
