<?php
declare(strict_types = 1);

use app\models\sys\users\Users;
use yii\bootstrap4\Html;
use yii\web\View;

/**
 * Шаблон письма для завершения регистрации
 * @var View $this view component instance
 * @var string $restoreUrl Ссылка на страницу восстановления
 * @var Users $user Пользователь, запросивший восстановление
 */
?>

<b>Ув. <?= $user->username ?>,</b><br/>
Вы зарегистрировались в <?= Yii::$app->name ?>.<br/>
Для того, чтобы завершить регистрацию, нужно установить новый пароль.
Для этого пройдите по указанной <?= Html::a('ссылке', $restoreUrl) ?> или скопируйте в адресную строку браузера адрес:
<br/>
<pre><?= $restoreUrl ?></pre>
<br/>
Если вы не зарегистрировались, то просто игнорируйте это письмо.
<br/>
С уважением,<br/>
робот поддержки <?= Yii::$app->name ?>.<br/>