<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var SimCard $model
 * @var ActiveForm $form
 */

use app\models\core\prototypes\ActiveFieldMap;
use kartik\form\ActiveForm;
use app\models\products\SimCard;
use yii\web\View;

?>

<?php foreach ($model->attributes() as $attribute): ?>
	<?php if ($model->isAttributeRequired($attribute)): ?>
		<div class="row">
			<div class="col-md-12">
				<?= $form->field($model, $attribute)->widget(ActiveFieldMap::class) ?>
			</div>
		</div>
	<?php endif ?>
<?php endforeach; ?>
