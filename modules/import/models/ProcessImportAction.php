<?php
declare(strict_types = 1);

namespace app\modules\import\models;

use Throwable;
use Yii;
use yii\base\Action;
use yii\web\Response;

/**
 * Class ProcessImportAction
 */
class ProcessImportAction extends Action {
	public array $mappingRules = [];
	public bool $ignoreErrors = true;

	/**
	 * @return string|Response
	 * @throws Throwable
	 */
	public function run(int $domain, string $modelClass) {
		$importModel = new ImportModel([
			'model' => $modelClass,
			'domain' => $domain,
			'mappingRules' => $this->mappingRules
		]);
		$messages = [];
		$isImportDone = $importModel->import($messages);

		if (Yii::$app->request->isAjax) {
			return $this->controller->asJson([
				'done' => $isImportDone,
				'percent' => $isImportDone?100:$importModel->percent,
				'messages' => $messages
			]);
		}
		if (!$this->ignoreErrors && [] !== $messages) { //на итерации найдены ошибки
			return $this->controller->render('@app/modules/import/views/import-errors', compact('messages', 'domain'));
		}
		if ($isImportDone) {
//			$count = $importModel->count;
			$importModel->clear();/*очищаем, чтоб не мусорить, поэтому count вызываем, он сохранится*/
			return $this->controller->render('@app/modules/import/views/import-done', [
				'controller' => get_class($this->controller),
				'model' => $importModel,
			]);
		}
		return $this->controller->render('@app/modules/import/views/import-progress', [
			'model' => $importModel,
			'controller' => get_class($this->controller)
		]);

	}
}