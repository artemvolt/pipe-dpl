<?php
declare(strict_types = 1);

/**
 * @var View $this
 */

use app\controllers\SellersController;
use yii\base\View;

?>
<div class="suggestion-item">
	<div class="suggestion-name">{{name}}</div>
	<div class="suggestion-links">
		<a href="<?= SellersController::to('edit') ?>?id={{id}}"
		   class="dashboard-button btn btn-xs btn-info pull-left">Редактировать<a/>
	</div>
	<div class="clearfix"></div>
</div>