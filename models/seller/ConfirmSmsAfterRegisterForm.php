<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\models\phones\PhoneNumberValidator;
use yii\base\Model;

/**
 * Class ConfirmSmsAfterRegisterForm
 * @package app\models\seller
 */
class ConfirmSmsAfterRegisterForm extends Model {
	public ?string $phoneNumber = null;
	public ?string $smsCode = null;
	public ?string $verificationToken = null;

	/**
	 * @return array
	 */
	public function rules():array {
		return [
			[['phoneNumber', 'smsCode', 'verificationToken'], 'required'],
			['phoneNumber', PhoneNumberValidator::class],
		];
	}
}