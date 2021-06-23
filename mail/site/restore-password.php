<?php
declare(strict_types = 1);

use app\models\sys\users\Users;
use yii\bootstrap4\Html;
use yii\mail\MessageInterface;
use yii\web\View;

/**
 * Шаблон письма со ссылкой восстановления пароля
 * @var View $this view component instance
 * @var MessageInterface $message the message being composed
 * @var string $restoreUrl Ссылка на страницу восстановления
 * @var Users $user Пользователь, запросивший восстановление
 */
?>

<b>Ув. <?= $user->username ?>,</b><br/>
Вами было запрошено восстановление пароля в <?= Yii::$app->name ?>.<br/>
Для того, чтобы установить новый пароль, пройдите по <?= Html::a('этой ссылке', $restoreUrl) ?>, или скопируйте в адресную строку браузера адрес
<br/>
<pre><?= $restoreUrl ?></pre>
<br/>
Если вы не запрашивали восстановление пароля, то просто игнорируйте это письмо.
<br/>
С уважением,<br/>
робот поддержки <?= Yii::$app->name ?>.<br/>