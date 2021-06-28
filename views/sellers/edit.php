<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Sellers $model
 */

use app\assets\ValidationAsset;
use app\models\seller\Sellers;
use yii\web\View;
use yii\bootstrap4\ActiveForm;

ValidationAsset::register($this);
?>

<?php $form = ActiveForm::begin(['enableAjaxValidation' => true]); ?>
<div class="panel">
	<div class="panel-hdr">
	</div>
	<div class="panel-container show">
		<div class="panel-content">
			<?= $this->render('subviews/editPanelBody', compact('model', 'form')) ?>
		</div>
		<div class="panel-content">
			<?= $this->render('subviews/editPanelFooter', compact('model', 'form')) ?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
