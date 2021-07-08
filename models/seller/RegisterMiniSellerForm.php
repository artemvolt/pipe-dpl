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

	public ?string $surname = null;
	public ?string $name = null;
	public ?string $patronymic = null;
	public ?string $phone_number = null;
	public ?string $email = null;
	public bool $accept_agreement = false;

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function rules():array {
		return [
			[['phone_number', 'surname', 'name', 'email'], 'required'],
			[['phone_number', 'surname', 'name', 'patronymic'], 'string'],
			[['phone_number', 'surname', 'name', 'patronymic'], 'trim'],
			['email', 'email'],
			['email', function() {
				if (null !== Users::findByEmail($this->email)) {
					$this->addError('email', 'Пользователь с таким почтовым адресом уже зарегистрирован');
				}
			}],
			['phone_number', PhoneNumberValidator::class],
			['phone_number', function() {
				if (null !== Users::findByLogin($this->phone_number)) {
					$this->addError('login', 'Такой логин уже занят');
				}
			}],
			['accept_agreement', 'boolean']
		];
	}
}