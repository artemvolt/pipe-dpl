<?php
declare(strict_types = 1);

namespace app\modules\fraud;

use pozitronik\core\traits\ModuleExtended;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Module;
use yii\console\Application as ConsoleApplication;

/**
 * Class FraudModule
 * @package app\modules\fraud
 */
class FraudModule extends Module implements BootstrapInterface {
	use ModuleExtended;

	/**
	 * @param Application $app
	 * @return void
	 */
	public function bootstrap($app):void {
		if ($app instanceof ConsoleApplication) {
			$this->controllerNamespace = 'app\modules\fraud\commands';
		}
	}
}
