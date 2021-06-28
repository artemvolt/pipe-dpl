<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var ImportModel $model
 * @var ControllerTrait $controller
 */

use app\modules\import\models\ImportModel;
use pozitronik\traits\traits\ControllerTrait;
use yii\bootstrap4\Html;
use yii\web\View;

?>

<div class="panel">
	<div class="panel-container">
		<div class="panel-content bg-success">
			Файл успешно импортирован в промежуточную таблицу. Импорт содержит <?= $model->count ?> записей.
		</div>
		<div class="panel-content bg-info">
			Нажмите "Далее" для экспорта в основные таблицы.<br/>
		</div>
		<div class="panel-content">
			<?= Html::a('Далее', $controller::to('process-import', ['domain' => $model->domain, 'modelClass' => $model->model]), [
				'class' => 'btn btn-success pull-right',
			]) ?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>