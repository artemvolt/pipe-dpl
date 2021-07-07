<?php
declare(strict_types = 1);

namespace app\models\seller\invite_link;

use app\models\phones\PhoneNumberValidator;
use app\models\phones\Phones;
use app\models\seller\SellerInviteLink;
use app\models\seller\SellerInviteLinkSearch;
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
}
