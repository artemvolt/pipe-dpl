<?php
declare(strict_types = 1);

namespace app\models\seller\invite_link;

use app\models\phones\PhoneNumberValidator;
use app\models\phones\Phones;
use app\models\seller\SellerInviteLinkSearch;
use app\models\store\Stores;
use yii\base\Model;

/**
 * Class CreateSellerInviteLinkForm
 * @package app\models\seller\invite_link
 */
class CreateSellerInviteLinkForm extends Model {
	public ?string $phone_number = null;
	public ?string $email = null;
	public ?int $store_id = null;

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
}
