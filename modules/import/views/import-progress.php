<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var ImportModel $model
 * @var ControllerTrait $controller
 */

use app\modules\import\ImportModuleAssets;
use app\modules\import\models\ImportModel;
use pozitronik\core\traits\ControllerTrait;
use yii\bootstrap4\Progress;
use yii\web\JsExpression;
use yii\web\View;

ImportModuleAssets::register($this);
$processUrl = $controller::to('process-import', ['domain' => $model->domain, 'modelClass' => $model->model]);
$this->registerJs(new JsExpression("RefreshProgress('$processUrl','importProgress');"));
?>

<div class="panel">
	<div class="panel-container">
		<div class="panel-content bg-info">
			Загруженные данные обрабатываются на сервере. Полоса прогресса отображает актуальный процент обработки.
		</div>
		<div class="panel-content">
			<?= Progress::widget([
				'id' => 'importProgress',
				'percent' => $model->percent
			]) ?>
		</div>
	</div>
</div>
