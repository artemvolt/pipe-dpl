<?php
declare(strict_types = 1);

namespace app\models\site;

use app\models\phones\Phones;
use app\models\sys\users\Users;
use app\modules\dol\components\exceptions\ServerDomainError;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use app\modules\dol\models\DolAPI;
use Exception;
use pozitronik\helpers\DateHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Авторизация по SMS
 *
 * @property Users|null $user
 * @property string $login Логин в системе
 * @property string $smsCode Код подтверждения
 */
class LoginSMSForm extends LoginForm {
	public $login;
	public ?string $smsCode = null;
	public bool $rememberMe = true;
	public bool $restore = false;

	/** @var null|Users */
	private ?Users $_user = null;
	/**
	 * @var string|null
	 * Если пользователь ввёл логин - то первый из его номеров,
	 * если по номеру - то указанный номер.
	 */
	private ?string $_phoneNumber = null;

	private bool $_smsSent = false;

	/**
	 * @return array the validation rules.
	 */
	public function rules():array {
		return [
			[['login'], 'required'],
			[['login'], 'string'],
			[['smsCode'], 'string', 'max' => 4],
			['rememberMe', 'boolean'],
			[['login'], function(string $attribute):void {
				if (null === $this->user) {
					$this->addError($attribute, 'Пользователь не найден');
				}
				if (!$this->hasErrors() && $this->user->deleted) {
					$this->addError($attribute, 'Пользователь заблокирован');
				}
			}],
			[['smsCode'], 'required', 'when' => function(LoginSMSForm $model) {
				return $model->_smsSent;
			}]

		];
	}

	/**
	 * @return array
	 */
	public function attributeLabels():array {
		return [
			'login' => 'Логин или телефонный номер',
			'smsCode' => 'Код подтверждения',
			'rememberMe' => 'Запомнить'
		];
	}

	/**
	 * @return Users|null
	 * @throws Exception
	 */
	public function getUser():?Users {
		if (null === $this->_user) {
			if (null === $this->_user = Users::findByLogin($this->login)) {
				if (null !== $this->_user = Users::findByPhoneNumber($this->login)) {
					$this->_phoneNumber = Phones::nationalFormat($this->login);
				}
			} else {
				$this->_phoneNumber = Phones::nationalFormat(ArrayHelper::getValue($this->_user->phones, '0'));
			}
		}
		return $this->_user;
	}

	/**
	 * @return bool
	 * @throws Exception
	 * @throws InvalidConfigException
	 */
	public function doSmsLogon():bool {
		if (!$this->validate()) return false;
		if (null === $this->_phoneNumber) {
			$this->addError($this->login, 'У пользователя не указан телефон');
			return false;
		}
		$dolAPI = new DolAPI();
		try {
			$dolAPI->smsLogon($this->_phoneNumber);
			$this->_smsSent = true;
			return true;
		} catch (ValidateServerErrors $e) {
			$this->addError('login', $e->getErrorsInOneRow());
		} catch (ServerDomainError $e) {
			$this->addError('login', $e->getMessage());
		}
		return false;
	}

	public function doConfirmSmsLogon():bool {
		if (!$this->validate()) return false;
		$dolAPI = new DolAPI();
		try {
			$isConfirm = $dolAPI->confirmSmsLogon($this->_phoneNumber, $this->smsCode);
			if ($isConfirm) {
				return Yii::$app->user->login($this->user, $this->rememberMe?DateHelper::SECONDS_IN_MONTH:0);
			}
			$this->addError('smsCode', 'Ошибки не возникло, но сервер вернул отрицательный ответ');
		} catch (ValidateServerErrors $e) {
			$this->addError('smsCode', $e->getErrorsInOneRow());
		} catch (ServerDomainError $e) {
			$this->addError('smsCode', $e->getMessage());
		}

		return false;
	}

}
