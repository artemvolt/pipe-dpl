<?php
declare(strict_types = 1);

/**
 * @var View $this
 */

use app\models\sys\users\Users;
use yii\web\View;

?>
Access granted for <?= Users::Current()->username ?>!