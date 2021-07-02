<?php
declare(strict_types = 1);

namespace app\modules\recogdol;

use app\modules\recogdol\exceptions\ConfigNotFoundException;
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

		$config = __DIR__.'/config/config.php';

		try {
			if (file_exists($config)) {
				Yii::configure($this, require $config);
				if (Yii::$app instanceof Application) {
					$this->controllerNamespace = 'app\modules\recogdol\commands';
				}
			} else {
				throw new ConfigNotFoundException();
			}
		} catch (Exception $e) {
			Yii::error(
				$e instanceof ConfigNotFoundException?$e->getMessage():$e->getTraceAsString(),
				'recogdol.api'
			);
		}
	}
}
