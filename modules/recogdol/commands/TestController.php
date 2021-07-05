<?php
declare(strict_types = 1);

namespace app\modules\recogdol\commands;

use app\modules\recogdol\models\RecogDolAPI;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\httpclient\Exception;

/**
 * Class TestController
 */
class TestController extends Controller {
	/** @var string $fileName */
	public $fileName;
	/** @var string $filePath */
	public $filePath;
	/** @var int $type */
	public $type;

	/**
	 * @inheritDoc
	 */
	public function options($actionID):array {
		return array_merge(parent::options($actionID), ['fileName', 'filePath', 'type']);
	}

	/**
	 * We cant test what does the host returns when sending document
	 * Example:
	 * yii recogdol/test/recognize-passport --filePath=/home/media/passport/file.jpg --fileName=file.jpeg --type=1
	 * type can be 1 (RecogDolAPI::METHOD_RECOGNIZE_FULL) or 2 (RecogDolAPI::METHOD_RECOGNIZE_SHORT)
	 * @return void
	 * @throws Exception
	 * @throws InvalidConfigException
	 */
	public function actionRecognizePassport():void {
		$res = (new RecogDolAPI())->recognize(
			$this->type,
			[
				'fileName' => $this->fileName,
				'filePath' => $this->filePath
			]
		);

		Yii::info(json_encode($res), 'recogdol.api');
	}
}