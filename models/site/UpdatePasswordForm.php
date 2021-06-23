<?php
declare(strict_types = 1);

namespace app\models\site;

use app\models\sys\users\Users;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Модель смены пароля пользователя.
 *
 * @property Users $user
 * @property string|null $oldPassword Предыдущий пароль
 * @property string|null $newPassword Новый пароль
 * @property string|null $newPasswordRepeat Повтор пароля для самопроверки
 */
class UpdatePasswordForm extends Model {
	public ?string $oldPassword = null;
	public ?string $newPassword = null;
	public ?string $newPasswordRepeat = null;
	/**
	 * @var bool Необходимость указания старого пароля при сбросе
	 */
	private bool $_requireOldPassword = true;

	/** @var Users|null */
	private ?Users $_user = null;

	/**
	 * @inheritDoc
	 */
	public function rules():array {
		return [
			[['oldPassword'], 'required', 'when' => function(UpdatePasswordForm $model, string $attribute) {
				return $model->_requireOldPassword;
			}],
			[['newPassword', 'newPasswordRepeat'], 'required'],
			[['newPasswordRepeat'], function(string $attribute):void {
				if (!$this->hasErrors() && $this->newPassword !== $this->newPasswordRepeat) {
					$this->addError('newPasswordRepeat', 'Введённые пароли должны совпадать');
				}
			}],
			[['oldPassword'], function(string $attribute):void {
				if ($this->_requireOldPassword && !$this->hasErrors() && !$this->user->validatePassword($this->oldPassword)) {
					$this->addError('oldPassword', 'Текущий пароль введён неверно');
				}
			}]

		];
	}

	/**
	 * @inheritDoc
	 */
	public function attributeLabels():array {
		return [
			'oldPassword' => 'Текущий пароль',
			'newPassword' => 'Новый пароль',
			'newPasswordRepeat' => 'Проверочный ввод нового пароля'
		];
	}

	/**
	 * Обновляет пароль пользователя
	 * @param bool $requireOldPassword Проверять ли наличие старого пароля перед заменой
	 * @return bool
	 */
	public function doUpdate(bool $requireOldPassword = true):bool {
		$this->_requireOldPassword = $requireOldPassword;
		if ($this->validate()) {
			$this->user->password = $this->newPassword;
			$this->user->is_pwd_outdated = false;
			return $this->user->save();
		}
		return false;
	}

	/**
	 * @return Users
	 * @throws InvalidConfigException
	 */
	public function getUser():Users {
		if (null === $this->_user) {
			throw new InvalidConfigException('Не указан пользователь для смены пароля');
		}
		return $this->_user;
	}

	/**
	 * @param Users $user
	 * @return void
	 */
	public function setUser(Users $user):void {
		$this->_user = $user;
	}

}
