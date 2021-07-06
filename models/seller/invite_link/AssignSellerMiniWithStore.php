<?php
declare(strict_types = 1);

namespace app\models\seller\invite_link;

use app\models\dealers\active_record\relations\RelDealersToStores;
use app\models\phones\PhoneNumberValidator;
use app\models\phones\Phones;
use app\models\seller\SellersSearch;
use app\models\store\active_record\relations\RelStoresToSellers;
use app\models\store\StoresSearch;
use DomainException;
use Yii;
use yii\base\Model;

/**
 * Class AssignSellerMiniWithStore
 * @package app\models\seller\invite_link
 */
class AssignSellerMiniWithStore extends Model {
	public $email;
	public $phone;
	public $storeId;

	/**
	 * @return array
	 */
	public function rules():array {
		return [
			['store_id', 'integer'],
			[['email', 'phone']],
			['email', 'email'],
			['phone', PhoneNumberValidator::class]
		];
	}

	public function assign():void {
		$currentUser = Yii::$app->user->identity;
		$phone = $this->phone?Phones::defaultFormat($this->phone):"";

		$existentStore = (new StoresSearch())->getById($this->storeId);
		/**
		 * @TODO как различить менеджера дилера
		 * и дилера торговой точки?
		 */
		if ($currentUser->isManager()) {
			$dealer = $existentStore->dealer;
			$existentDealerStore = (new RelDealersToStores())->findExistentDealerStore($dealer->id, $existentStore->id);
			if (null === $existentDealerStore) {
				throw new DomainException("Торговая точка относится к другому дилеру");
			}
		}

		$existentSeller = (new SellersSearch())->findMiniSellerWithPhone($phone);
		if (null === $existentSeller) {
			$existentSeller = (new SellersSearch())->findMiniSellerWithEmail($this->email);
			if (null === $existentSeller) {
				throw new DomainException("Не получилось найти пользователя");
			}
		}
		$assign = RelStoresToSellers::assign($existentStore->id, $existentSeller->id);
		if (!$assign->save()) {
			throw new DomainException(
				"Не получилось привязать менеджера к точке.".
				implode(".", $assign->getFirstErrors())
			);
		}
	}
}