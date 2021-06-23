<?php
declare(strict_types = 1);

namespace app\models\site;

use app\models\sys\users\Users;
use yii\base\Model;

/**
 * Модель регистрации нового пользователя
 * Пока без проверок почты, без модерации, и прочей обвязки
 *
 * @property string $username
 * @property string $login
 * @property string $password
 * @property string $passwordRepeat
 * @property string $email
 */
class RegistrationForm extends Model {
	public $username;
	public $login;
	public $password;
	public $passwordRepeat;
	public $email;

	/**
	 * @return array the validation rules.
	 */
	public function rules():array {
		return [
			[['username', 'login', 'password', 'passwordRepeat', 'email'], 'required'],
			[['email'], 'email'],
			[['email'], function(string $attribute):void {
				if (!$this->hasErrors() && null !== Users::findByEmail($this->email)) {
					$this->addError('email', 'Пользователь с таким почтовым адресом уже зарегистрирован');
				}
			}],
			[['login'], function(string $attribute):void {
				if (!$this->hasErrors() && null !== Users::findByLogin($this->login)) {
					$this->addError('login', 'Такой логин уже занят');
				}
			}],
			[['passwordRepeat'], function(string $attribute):void {
				if (!$this->hasErrors() && $this->password !== $this->passwordRepeat) {
					$this->addError('passwordRepeat', 'Введённые пароли должны совпадать');
				}
			}]
		];
	}

	/**
	 * @return array
	 */
	public function attributeLabels():array {
		return [
			'username' => 'Представьтесь, пожалуйста:',
			'login' => 'Логин',
			'password' => 'Пароль',
			'passwordRepeat' => 'Пароль ещё раз',
			'email' => 'Почтовый адрес'
		];
	}

	/**
	 * @return bool
	 */
	public function doRegister():bool {
		if ($this->validate()) {
			$newUser = new Users([
				'login' => $this->login,
				'username' => $this->username,
				'password' => $this->password,
				'email' => $this->email
			]);
			return $newUser->save();
		}
		return false;
	}

}
