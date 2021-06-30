<?php
declare(strict_types = 1);

namespace app\modules\api\connectors\vet_expert;

use app\models\abonents\Abonents;
use app\models\phones\Phones;
use app\modules\api\signatures\SignatureService;
use app\modules\api\signatures\SignatureServiceFactory;
use DateTime;
use InvalidArgumentException;
use yii\base\Arrayable;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;

/**
 * Источник данных для формирования подписки в VetExpert.
 * Для удобства накручивания валидации при инициализации атрибутов, а также для формирования body-параметров для API.
 *
 * Class SubscriptionParams
 * @package app\modules\api\connectors\vetexpert
 */
class SubscriptionParams extends BaseObject implements Arrayable {
	/**
	 * @var string
	 */
	private string $_phone = '';
	/**
	 * @var string
	 */
	private string $_email = '';
	/**
	 * @var string
	 */
	private string $_firstName = '';
	/**
	 * @var string|null
	 */
	private ?string $_middleName = null;
	/**
	 * @var string
	 */
	private string $_lastName = '';
	/**
	 * @var string
	 */
	private string $_subscriptionTo = '';
	/**
	 * @var SignatureService компонент для подписи body-параметров.
	 */
	private SignatureService $_signatureService;

	/**
	 * SubscriptionParams constructor.
	 * @param array $config
	 * @throws InvalidConfigException
	 */
	public function __construct($config = []) {
		parent::__construct($config);

		$this->_signatureService = SignatureServiceFactory::build('vet-expert');
	}

	/**
	 * @param string $phone
	 */
	public function setPhone(string $phone):void {
		if (!Phones::isValidNumber($phone) || (null === $formattedPhone = Phones::nationalFormat($this->_phone))) {
			throw new InvalidArgumentException('Некорректное значение телефонного номера.');
		}

		/** @var string $formattedPhone */
		$this->_phone = $formattedPhone;
	}

	/**
	 * @return string
	 */
	public function getPhone():string {
		return $this->_phone;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email):void {
		$this->_email = $email;
	}

	/**
	 * @return string
	 */
	public function getEmail():string {
		return $this->_email;
	}

	/**
	 * @param string $firstName
	 */
	public function setFirstName(string $firstName):void {
		$this->_firstName = $firstName;
	}

	/**
	 * @return string
	 */
	public function getFirstName():string {
		return $this->_firstName;
	}

	/**
	 * @param string|null $middleName
	 */
	public function setMiddleName(?string $middleName = null):void {
		$this->_middleName = $middleName;
	}

	/**
	 * @return string|null
	 */
	public function getMiddleName():?string {
		return $this->_middleName;
	}

	/**
	 * @param string $lastName
	 */
	public function setLastName(string $lastName):void {
		$this->_lastName = $lastName;
	}

	/**
	 * @return string
	 */
	public function getLastName():string {
		return $this->_lastName;
	}

	/**
	 * @param DateTime $date
	 */
	public function setSubscriptionTo(DateTime $date):void {
		$this->_subscriptionTo = $date->format('d.m.Y');
	}

	/**
	 * @return string
	 */
	public function getSubscriptionTo():string {
		return $this->_subscriptionTo;
	}

	/**
	 * {@inheritdoc}
	 */
	public function fields():array {
		return [
			'phone' => $this->_phone,
			'email' => $this->_email,
			'first_name' => $this->_firstName,
			'last_name' => $this->_lastName,
			'middle_name' => $this->_middleName,
			'subscription_to' => $this->_subscriptionTo
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function extraFields():array {
		return ['sign' => $this->getParamsSignature($this->fields())];
	}

	/**
	 * @param array $fields
	 * @param array $expand
	 * @param bool $recursive
	 * @return array
	 */
	public function toArray(array $fields = [], array $expand = [], $recursive = true):array {
		return array_merge($this->fields(), $this->extraFields());
	}

	/**
	 * @param Abonents $abonent
	 * @return static
	 * @throws InvalidConfigException
	 */
	public static function createInstance(Abonents $abonent):self {
		return new static([
			'phone' => $abonent->phone,
			'lastName' => $abonent->surname,
			'middleName' => $abonent->patronymic,
			'firstName' => $abonent->name
		]);
	}

	/**
	 * @param array $params
	 * @return string подпись всех параметров запроса.
	 */
	private function getParamsSignature(array $params):string {
		ksort($params);
		array_walk($params, static function(&$item, $key) {
			$item = "{$key}={$item}";
		});

		return $this->_signatureService->sign(implode('&', $params));
	}
}