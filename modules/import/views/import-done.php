<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var ImportModel $model
 * @var ControllerTrait $controller
 */

use app\modules\import\models\ImportModel;
use pozitronik\core\traits\ControllerTrait;
use pozitronik\helpers\Utils;
use yii\bootstrap4\Html;
use yii\web\View;

?>

<div class="panel">
	<div class="panel-container">
		<div class="panel-content bg-success">
			Готово!
		</div>
		<div class="panel-tag">
			Обработано: <?= Utils::pluralForm($model->count, ['строка', 'строки', 'строк']) ?>
			<br/>
			С ошибкой: <?= Utils::pluralForm($model->errorCount, ['строка', 'строки', 'строк']) ?>.
		</div>
		<div class="panel-content">
			<?= Html::a('Загрузить ещё что-нибудь', $controller::to('import'), [
				'class' => 'btn btn-success pull-right',
			]) ?>
			<?= Html::a('На главную', $controller::to('index'), [
				'class' => 'btn btn-success pull-right',
			]) ?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>