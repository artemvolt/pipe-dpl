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
	/** @var string $fieldName */
	public $fieldName;
	/** @var string $filePath */
	public $filePath;
	/** @var string $profile */
	public $profile;
	/** @var int $type */
	public $type;

	/**
	 * @inheritDoc
	 */
	public function options($actionID):array {
		return array_merge(parent::options($actionID), ['fieldName', 'filePath', 'type', 'profile']);
	}

	/**
	 * We cant test what does the host returns when sending document
	 * Example:
	 * yii recogdol/test/recognize-passport --filePath=/home/media/passport/file.jpg --fieldName=image --type=2 --profile=dol
	 * type can be 1 (RecogDolAPI::METHOD_RECOGNIZE_FULL) or 2 (RecogDolAPI::METHOD_RECOGNIZE_SHORT).
	 * NOTE: we can use only type=2 now.
	 * @return void
	 * @throws Exception
	 * @throws InvalidConfigException
	 */
	public function actionRecognizePassport():void {
		$res = (new RecogDolAPI())->recognize(
			$this->type,
			[
				'fieldName' => $this->fieldName,
				'filePath' => $this->filePath
			],
			[
				'profile' => $this->profile
			]
		);

		Yii::info(json_encode($res), 'recogdol.api');
	}
}