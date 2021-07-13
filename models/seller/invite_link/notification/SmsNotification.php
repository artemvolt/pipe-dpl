<?php
declare(strict_types = 1);

namespace app\models\seller\invite_link\notification;

use app\modules\dol\models\DolAPI;
use Yii;
use yii\base\InvalidConfigException;
use Exception;

/**
 * Class SmsNotification
 * @package app\models\seller\invite_link\notification
 */
class SmsNotification {
	protected $transport;

	public function __construct() {
		$this->transport = Yii::$container->get(DolAPI::class);
	}

	/**
	 * @param string $phone
	 * @param string $url
	 * @throws Exception
	 */
	public function notify(string $phone, string $url):void {
		$this->transport->sendSms($phone, "Ваша ссылка: ".$url);
	}

	/**
	 * @param string $phone
	 * @throws InvalidConfigException
	 * @throws Exception
	 */
	public function smsLogon(string $phone):void {
		$this->transport->smsLogon($phone);
	}
}
