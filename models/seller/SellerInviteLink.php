<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\models\phones\Phones;
use app\models\seller\active_record\SellerInviteLinkAr;
use DateInterval;
use DateTime;
use InvalidArgumentException;
use Yii;
use yii\base\Exception;

/**
 * Class SellerInviteLink
 * @package app\models\seller
 */
class SellerInviteLink extends SellerInviteLinkAr {
	public const TOKEN_EXPIRED_AFTER_COUNT_DAYS = 1;

	/**
	 * @return string
	 */
	public function inviteUrl():string {
		$params = Yii::$app->params;
		return $params['frontend']['scheme']."://".
			$params['frontend']['host']."/sm/".
			$this->token;
	}

	/**
	 * @param int $numDay
	 * @return $this
	 */
	public function expiredCountDays(int $numDay):self {
		$date = new DateTime();
		$date->add(new DateInterval("P{$numDay}D"));
		$this->expired_at = $date->format('Y-m-d H:i:s');
		return $this;
	}

	/**
	 * @return self
	 * @throws Exception
	 * @throws Exception
	 */
	public function generateToken():self {
		$this->token = Yii::$app->security->generateRandomString(6);
		return $this;
	}

	/**
	 * @param int $storeId
	 * @param string|null $phoneNumber
	 * @param string|null $email
	 * @return $this
	 * @throws Exception
	 */
	public static function createLink(int $storeId, ?string $phoneNumber, ?string $email):self {
		$link = new SellerInviteLink([
			'phone_number' => Phones::defaultFormat($phoneNumber),
			'store_id' => $storeId,
			'email' => $email
		]);
		$link->expiredCountDays(self::TOKEN_EXPIRED_AFTER_COUNT_DAYS);
		$link->generateToken();
		return $link;
	}

	/**
	 * @param string|null $phone
	 * @param null $email
	 * @return $this
	 */
	public function edit(?string $phone = "", ?string $email = ""):self {
		if (empty($phone) && empty($email)) {
			throw new InvalidArgumentException("Телефон либо email должны быть заполнены");
		}
		$this->phone_number = $phone?Phones::defaultFormat($phone):"";
		$this->email = $email?:"";
		return $this;
	}
}
