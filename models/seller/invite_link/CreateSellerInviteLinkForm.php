<?php
declare(strict_types = 1);

namespace app\models\seller\invite_link;

use app\components\exceptions\MuteManager;
use app\components\exceptions\ValidateException;
use app\models\phones\PhoneNumberValidator;
use app\models\phones\Phones;
use app\models\seller\invite_link\notification\EmailNotification;
use app\models\seller\SellerInviteLink;
use app\models\seller\SellerInviteLinkSearch;
use app\models\store\Stores;
use app\modules\dol\models\DolAPI;
use DomainException;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\di\NotInstantiableException;

/**
 * Class CreateSellerInviteLinkForm
 * @package app\models\seller\invite_link
 */
class CreateSellerInviteLinkForm extends Model {
	public $phone_number;
	public $email;
	public $store_id;

	/**
	 * @var DolAPI $smsTransport
	 */
	protected $smsTransport;
	/**
	 * @var MuteManager $mute
	 */
	protected $mute;

	/**
	 * @var EmailNotification $emailTransport
	 */
	protected $emailTransport;

	/**
	 * CreateSellerInviteLinkForm constructor.
	 * @param array $config
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
	 */
	public function __construct($config = []) {
		parent::__construct($config);
		$this->smsTransport = Yii::$container->get(DolAPI::class);
		$this->emailTransport = new EmailNotification();
		$this->mute = new MuteManager();
	}

	public function rules():array {
		return [
			[['phone_number', 'email'], 'filter', 'filter' => 'trim'],
			['store_id', 'required'],
			[['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::class, 'targetAttribute' => ['store_id' => 'id']],
			[['phone_number'], PhoneNumberValidator::class],
			['email', 'email'],
			[['phone_number'], function() {
				if ($this->phone_number && (new SellerInviteLinkSearch())->findByPhone(Phones::defaultFormat($this->phone_number))) {
					$this->addError('phone_number', 'Номер уже существует');
				}
			}],
			[['email'], function() {
				if ($this->email && (new SellerInviteLinkSearch())->findByEmail($this->email)) {
					$this->addError('email', 'Email уже существует');
				}
			}],
		];
	}

	/**
	 * @return bool
	 */
	public function beforeValidate():bool {
		if (empty($this->phone_number) && empty($this->email)) {
			$this->addError('email', $message = "Email или телефон должны быть заполнены ");
			$this->addError('phone_number', $message);
			return false;
		}
		return parent::beforeValidate();
	}

	/**
	 * @return SellerInviteLink
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function create():SellerInviteLink {
		if (!$this->validate()) {
			throw new ValidateException($this->getErrors());
		}

		$link = SellerInviteLink::createLink((int)$this->store_id, $this->phone_number, $this->email);

		if (!$link->save()) {
			throw new DomainException("Ошибка создания записи. ".implode(". ", $link->getFirstErrors()));
		}

		if (!empty($link->phone_number)) {
			$this->sendSms($link->phone_number, $link->inviteUrl());
		}

		if (!empty($link->email)) {
			$this->sendEmail($link->email, $link->inviteUrl());
		}

		return $link;
	}

	/**
	 * @param string $email
	 * @param string $url
	 * @throws Throwable
	 */
	protected function sendEmail(string $email, string $url):void {
		$this->mute->mute(function() use ($email, $url) {
			$this->emailTransport->notify($email, $url);
		});
	}

	/**
	 * @param string $phone
	 * @param string $url
	 * @throws Throwable
	 */
	protected function sendSms(string $phone, string $url):void {
		$this->mute->mute(function() use ($phone, $url) {
			$this->smsTransport->sendSms(
				$phone,
				"Ваша ссылка: ".$url
			);
		});

	}
}