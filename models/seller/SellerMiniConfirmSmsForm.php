<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\models\phones\PhoneNumberValidator;
use yii\base\Model;

/**
 * Class SellerMiniConfirmSmsForm
 * @package app\models\seller
 */
class SellerMiniConfirmSmsForm extends Model {
	public ?string $sms = null;
	public ?string $phone_number = null;

	/**
	 * @inheritDoc
	 */
	public function rules():array {
		return [
			['phone_number', PhoneNumberValidator::class],
			[['phone_number', 'sms'], 'required'],
			['phone_number', function() {
				if (null === (new SellersSearch())->findMiniSellerWithPhone($this->phone_number)) {
					$this->addError("phone_number", 'Продавец с данным номером не найден');
				}
			}]
		];
	}
}