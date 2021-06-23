<?php
declare(strict_types = 1);

/**
 * @var View $this
 */

use app\controllers\UsersController;
use yii\base\View;

?>
<div class="suggestion-item">
	<div class="suggestion-name">{{username}}</div>
	<div class="suggestion-links">
		<a href="<?= UsersController::to('profile') ?>?id={{id}}"
		   class="dashboard-button btn btn-xs btn-info float-left">Профиль<a/>
	</div>
	<div class="clearfix"></div>
</div>