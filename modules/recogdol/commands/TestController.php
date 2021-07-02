<?php
declare(strict_types = 1);

namespace app\modules\recogdol\commands;

use app\modules\recogdol\models\RecogDolAPI;
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

	/**
	 * @inheritDoc
	 */
	public function options($actionID):array {
		return array_merge(parent::options($actionID), ['fileName', 'filePath']);
	}

	/**
	 * We cant test what does the host returns when sending document
	 * Example:
	 * yii recogdol/test/recognize-passport --filePath=/home/media/passport/file.jpg --fileName=file
	 * @return array
	 * @throws Exception
	 * @throws InvalidConfigException
	 */
	public function actionRecognizePassport():array {
		return (new RecogDolAPI())->recognize(
			RecogDolAPI::METHOD_RECOGNIZE_SHORT,
			[
				'fileName' => $this->fileName,
				'filePath' => $this->filePath
			]
		);
	}
}