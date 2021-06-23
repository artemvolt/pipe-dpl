<?php
declare(strict_types = 1);

use app\models\seller\Sellers;
use app\models\managers\Managers;
use yii\bootstrap4\Html;
use yii\web\View;

/**
 * Шаблон письма со ссылкой восстановления пароля
 * @var View $this view component instance
 * @var string $entityUrl Ссылка на страницу продавца
 * @var string $entityName Сущность
 * @var Sellers|Managers $entity
 * @var array $errors
 */
?>
Здравствуйте,<br>
<?= $entityName ?> <?= $entity->fio ?> не прошел успешно регистрацию в <?= Yii::$app->name ?>.<br>
Необходимо исправить следующие ошибки:<br>
<ul>
	<?php foreach ($errors as $index => $error): ?>
		<li><?= $error ?></li>
	<?php endforeach; ?>
</ul>

Для этого пройдите по указанной <?= Html::a('ссылке', $entityUrl) ?> или скопируйте в адресную строку браузера адрес:
<br>
<pre><?= $entityUrl ?></pre>
<br>
С уважением,<br/>
робот поддержки <?= Yii::$app->name ?>.<br>