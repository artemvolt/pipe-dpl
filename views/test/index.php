<?php /** @noinspection PhpRedundantClosingTagInspection */
declare(strict_types = 1);

/**
 * @var View $this
 * @var string[] $actions
 */

use app\controllers\TestController;
use yii\bootstrap4\Html;
use yii\web\View;

?>
<?php foreach ($actions as $action): ?>
	<?= Html::a($action, TestController::to($action)) ?>
	<div class="clearfix"></div>
<?php endforeach; ?>
