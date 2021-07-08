<?php
declare(strict_types = 1);

namespace app\models\seller\invite_link\notification;

use app\modules\dol\models\DolAPI;
use RuntimeException;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
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
		$response = $this->transport->sendSms($phone, "Ваша ссылка: ".$url);
		$this->validateResponse($response);
	}

	/**
	 * @param string $phone
	 * @throws InvalidConfigException
	 * @throws Exception
	 */
	public function smsLogon(string $phone):void {
		$response = (clone $this->transport)->smsLogon($phone);
		$this->validateResponse($response);
	}

	/**
	 * @param array $response
	 * @throws Exception
	 */
	protected function validateResponse(array $response):void {
		if (!ArrayHelper::getValue($response, 'success', false)) {
			throw new RuntimeException("Не получилось отправить смс");
		}
	}
}
