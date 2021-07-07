<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\models\phones\PhoneNumberValidator;
use app\models\phones\Phones;
use app\models\store\Stores;
use yii\base\Model;

/**
 * Class SellerMiniAssignWithStoreForm
 * @package app\models\seller
 */
class SellerMiniAssignWithStoreForm extends Model {
	public $phone_number;
	public $email;
	public $store_id;

	/**
	 * @return string[]
	 */
	public function attributeLabels():array {
		return [
			'phone_number' => 'Телефон',
			'email' => 'Email',
			'store_id' => 'Магазин'
		];
	}

	/**
	 * @return array
	 */
	public function rules():array {
		return [
			['store_id', 'required'],
			[['phone_number', 'email'], 'filter', 'filter' => 'trim'],
			['phone_number', PhoneNumberValidator::class],
			['email', 'email'],
			[['store_id'], 'exist', 'skipOnError' => false, 'targetClass' => Stores::class, 'targetAttribute' => ['store_id' => 'id']],
			[['phone_number'], function() {
				if ($this->phone_number) {
					$phone = Phones::defaultFormat($this->phone_number);
					if (null === (new SellersSearch())->findMiniSellerWithPhone($phone)) {
						$this->addError("phone_number", 'Не получилось найти продавца по номеру телефона');
					}
				}
			}],
			[['email'], function() {
				if ($this->email && null === (new SellersSearch())->findMiniSellerWithEmail($this->email)) {
					$this->addError("email", 'Не получилось найти продавца по email');
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
