<?php
declare(strict_types = 1);

namespace app\models\site;

use app\models\core\TemporaryHelper;
use app\models\phones\Phones;
use app\models\sys\users\Users;
use app\modules\dol\models\DolAPI;
use Exception;
use pozitronik\helpers\DateHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\httpclient\Exception as HttpException;

/**
 * Авторизация по SMS
 *
 * @property Users|null $user
 * @property string $login Логин в системе
 * @property string $smsCode Код подтверждения
 * @property-read DolAPI $dolAPI Объект API
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

	private ?DolAPI $_dolAPI = null;

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
//		if (!$this->validate()) return false; <== больше не нужно, т.к. валидацией мы отсекали проверку пользователей, отсутствующих в системе.
		if (null === $this->user) {/*подходящей учётки пользователя нет в системе, надо спросить у DOL*/
			if (!$this->DolLogon()) {
				/*DOL об этом чуваке не в курсе*/
				$this->addError($this->login, $this->dolAPI->errorMessage);
				return false;
			}

		} else {/*пользователь с таким логином в системе есть*/
			if (null === $this->_phoneNumber) {/*но к этому логину не привязан телефон*/
				$this->addError($this->login, 'У пользователя не указан телефон');
				return false;
			}
			/*авторизуем в DOL (только для смс-авторизации)*/
			if (!$this->DolLogon()) {
				/*DOL об этом чуваке не в курсе*/
				$this->addError($this->login, $this->dolAPI->errorMessage);
				return false;
			}
		}
		/**
		 * Учётка есть в системе. Разрешаем ему войти, через смс-подтверждение в DOL.
		 * либо
		 * Учётка есть в DOL. Разрешаем ему войти, проксируя авторизацию в DOL (с последующим стягиванием данных)
		 */
		return true;
	}

	/**
	 * @return bool
	 * @throws HttpException
	 * @throws InvalidConfigException
	 */
	private function DolLogon():bool {
		$this->dolAPI->smsLogon($this->_phoneNumber);
		if ($this->dolAPI->success) {
			$this->_smsSent = true;
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 * @throws InvalidConfigException
	 * @throws HttpException
	 */
	public function doConfirmSmsLogon():bool {
		//if (!$this->validate()) return false; <== больше не нужно, т.к. валидацией мы отсекали проверку пользователей, отсутствующих в системе.
		$response = $this->dolAPI->confirmSmsLogon($this->_phoneNumber, $this->smsCode);
		if ($this->dolAPI->success) {
			if (null === $this->user) {/*мы авторизовали в DOL пользователя, которого нет в системе.*/
				/**
				 * Теперь нам с этим токеном надо получить данные этого юзера, и перенести их к нам.
				 */
				$this->dolAPI->authToken->loadFromResponseArray($response);
				$responseData = $this->dolAPI->getUserProfile(/*todo*/);
				if (!$this->dolAPI->success) {
					$this->addError($this->smsCode, $this->dolAPI->errorMessage);
					return false;
				}
				if (!$this->createUserFromDol($responseData)) {/*сразу генерим себе пользователя*/
					$this->addError($this->login, TemporaryHelper::Errors2String($this->user->errors)/*например*/);
					return false;
				}
			}
			return Yii::$app->user->login($this->user, $this->rememberMe?DateHelper::SECONDS_IN_MONTH:0);
		}
		$this->addError('smsCode', $this->dolAPI->errorMessage);
		return false;
	}

	/**
	 * Создаёт нового пользователя по данным из DOL
	 * @param array $dolData Данные, полученные из DOL
	 * @return bool
	 */
	private function createUserFromDol(array $dolData):bool {
		/*todo*/
		$this->user = new Users([
			'login' => $this->_phoneNumber,
			'username' => $this->_phoneNumber,
			'password' => Users::DEFAULT_PASSWORD,
			'comment' => "Пользователь создан при сквозной авторизации в DOL ",
			'email' => $this->email,
			'phones' => $this->_phoneNumber
		]);
//		if (!$user->save()) return false;
//		$this->relatedUser = $user;
		return true;
	}

	/**
	 * @return DolAPI
	 */
	public function getDolAPI():DolAPI {
		if (null === $this->_dolAPI) $this->_dolAPI = new DolAPI();
		return $this->_dolAPI;
	}

}
