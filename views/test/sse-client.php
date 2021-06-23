<?php
declare(strict_types = 1);

/**
 * @var View $this
 */

use app\assets\SSEAsset;
use yii\web\View;

SSEAsset::register($this);

?>

<div id="list" style="width: 100%; min-height: 300px"></div>
