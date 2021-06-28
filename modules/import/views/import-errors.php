<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var int $domain
 * @var array $messages
 * @var ControllerTrait $controller
 */

use pozitronik\helpers\Utils;
use pozitronik\traits\traits\ControllerTrait;
use yii\bootstrap4\Html;
use yii\web\View;

?>

<div class="panel">
	<div class="panel-hdr">
		<div class="panel-content bg-warning">
			Ошибки на этапе импорта
		</div>
	</div>
	<div class="panel-container">
		<div class="panel-content">
			<?php if ([] === $messages): ?>
				<?= Html::label('Нет ошибок') ?>
			<?php else: ?>
				<?= Html::label('Ошибки:') ?>
				<br/>
				<?php Utils::log($messages); ?>
			<?php endif; ?>
		</div>


		<div class="panel-content">
			<?php if ([] === $messages): ?>
				<?= Html::a('Повторить', $controller::to('process-import', ['domain' => $domain], ['class' => 'btn btn-warning pull-left'])) ?>
				<?= Html::a('Загрузить что-то ещё', $controller::to('import', ['domain' => $domain], ['class' => 'btn btn-warning pull-left'])) ?>
			<?php else: ?>
			<?php endif; ?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>


