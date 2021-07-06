<?php
declare(strict_types = 1);

namespace app\models\seller\invite_link\notification;

use Exception;
use RuntimeException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\mail\MailerInterface;

/**
 * Class EmailNotification
 * @package app\models\seller\invite_link\notification
 */
class EmailNotification {
	private ?MailerInterface $mailer;

	public function __construct(?MailerInterface $mailer = null) {
		$this->mailer = $mailer?:Yii::$app->mailer;
	}

	/**
	 * @param string $email
	 * @param string $url
	 * @throws Exception
	 */
	public function notify(string $email, string $url) {
		$from = ArrayHelper::getValue(Yii::$app->params, 'emailRobot.from');
		if (empty($email)) {
			throw new RuntimeException("Не настроена почта в конфигурации");
		}

		$isSent = $this->mailer
			->compose()
			->setFrom($from)
			->setSubject("Приглашение")
			->setTo($email)
			->setHtmlBody("Ваша ссылка: ".$url)
			->send();
		if (false === $isSent) {
			throw new RuntimeException("Не получилось отправить письмо с приглашением");
		}
	}
}