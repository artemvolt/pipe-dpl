<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\models\phones\PhoneNumberValidator;
use app\models\sys\users\Users;
use yii\base\Model;

/**
 * Class MiniSeller
 * @property string $surname
 * @property string $name
 * @property string $patronymic
 * @property string $phone_number
 * @property string $email
 * @property bool $accept_agreement
 */
class RegisterMiniSellerForm extends Model {

	public ?string $phone_number = null;
	public bool $accept_agreement = false;

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function rules():array {
		return [
			[['phone_number'], 'filter', 'filter' => 'trim'],
			[['phone_number', 'accept_agreement'], 'required'],
			['accept_agreement', 'boolean'],
			[['phone_number'], 'string'],
			['phone_number', PhoneNumberValidator::class],
			['phone_number', function() {
				if (null !== Users::findByLogin($this->phone_number)) {
					$this->addError('login', 'Такой номер телефона уже существует');
				}
			}]
		];
	}
}
