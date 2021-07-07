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
use app\modules\dol\models\DolAPI;
use DomainException;
use Throwable;
use Yii;
use yii\base\Model;

/**
 * Class EditSellerInviteLink
 * @package app\models\seller\invite_link
 */
class EditSellerInviteLink extends Model {
	public $phone_number;
	public $email;
	public $existentIdLink;
	public $repeatPhoneNotify;
	public $repeatEmailNotify;

	/**
	 * @var EmailNotification $emailNotify
	 */
	protected $emailNotify;
	/**
	 * @var MuteManager $mute
	 */
	protected $mute;

	/**
	 * @var DolAPI $smsTransport
	 */
	protected $smsTransport;

	public function init():void {
		parent::init();
		$this->emailNotify = new EmailNotification();
		$this->mute = new MuteManager();
		$this->smsTransport = Yii::$container->get(DolAPI::class);
	}

	/**
	 * @return string[]
	 */
	public function attributeLabels():array {
		return [
			'phone_number' => 'Телефон',
			'email' => 'Email',
			'repeatPhoneNotify' => 'Отправить повторно sms',
			'repeatEmailNotify' => 'Отправить повторно email',
		];
	}

	/**
	 * @return array
	 */
	public function rules():array {
		return [
			[['phone_number', 'email'], 'filter', 'filter' => 'trim'],
			['phone_number', PhoneNumberValidator::class],
			['email', 'email'],
			[['existentIdLink'], 'exist', 'skipOnError' => false, 'targetClass' => SellerInviteLink::class, 'targetAttribute' => 'id'],
			[['repeatPhoneNotify', 'repeatEmailNotify'], 'boolean'],
			[['phone_number'], function() {
				if (!empty($this->phone_number) && $find = (new SellerInviteLinkSearch())->findByPhone(Phones::defaultFormat($this->phone_number))) {
					if ($find->id !== $this->existentIdLink) {
						$this->addError('phone_number', 'Номер уже существует');
					}
				}
			}],
			[['email'], function() {
				if (!empty($this->email) && $find = (new SellerInviteLinkSearch())->findByEmail($this->email)) {
					if ($find->id !== $this->existentIdLink) {
						$this->addError('email', 'Email уже существует');
					}
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

	public function edit():SellerInviteLink {
		if (!$this->validate()) {
			throw new ValidateException($this->getErrors());
		}

		$existentLink = (new SellerInviteLinkSearch())->getById((int)$this->existentIdLink);
		$existentLink->edit($this->phone_number, $this->email);
		if (!$existentLink->save()) {
			throw new DomainException(
				"Не получилось обновить ссылку.".
				implode(". ", $existentLink->getFirstErrors())
			);
		}
		if ($this->repeatPhoneNotify && $this->phone_number) {
			$this->sendSms($existentLink->phone_number, $existentLink->inviteUrl());
		}
		if ($this->repeatEmailNotify && $this->email) {
			$this->sendEmail($existentLink->email, $existentLink->inviteUrl());
		}
		return $existentLink;
	}

	/**
	 * @param string $phoneNumber
	 * @param string $url
	 * @throws Throwable
	 */
	protected function sendSms(string $phoneNumber, string $url):void {
		$this->mute->mute(function() use ($phoneNumber, $url) {
			$this->smsTransport->sendSms($phoneNumber, "Ваша ссылка: ".$url);
		});
	}

	/**
	 * @param string $email
	 * @param string $url
	 * @throws Throwable
	 */
	protected function sendEmail(string $email, string $url):void {
		$this->mute->mute(function() use ($email, $url) {
			$this->emailNotify->notify($email, $url);
		});
	}
}
