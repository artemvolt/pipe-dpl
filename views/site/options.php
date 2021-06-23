<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var array $boolOptions
 * @var array $optionsLabels
 */

use app\assets\OptionsAsset;
use app\controllers\AjaxController;
use kartik\switchinput\SwitchInput;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;

OptionsAsset::register($this);
?>

<div class="hpanel">
	<div class="panel-hdr">
	</div>
	<div class="panel-container show">
		<div class="panel-content">
			<?php foreach ($boolOptions as $name => $value): ?>
				<?php $id = md5($name) ?>
				<div class="row">
					<div class="col-md-1">
						<?= SwitchInput::widget([
							'name' => $name,
							'tristate' => false,
							'value' => $value,
							'disabled' => false,
							'pluginOptions' => [
								'onText' => 'On',
								'offText' => 'Off'
							],
							'pluginEvents' => [
								"switchChange.bootstrapSwitch" => new JsExpression('function (event, state) {SetSystemOptionBool("'.$name.'", state, "'.$id.'", "'.AjaxController::to('set-system-option').'")}')
							],
						]) ?>
					</div>
					<div class="col-md-6" id="<?= $id ?>">
						<?= ArrayHelper::getValue($optionsLabels, $name, $name) ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

