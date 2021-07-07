<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\components\exceptions\ValidateException;
use app\models\phones\Phones;
use app\models\store\active_record\relations\RelStoresToSellers;
use app\models\sys\users\Users;
use DomainException;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class SellerMiniService
 * @package app\models\seller
 */
class SellerMiniService {

	/**
	 * @var Users|null
	 */
	private ?Users $currentUser;

	/**
	 * SellerMiniService constructor.
	 * @param Users|null $currentUser
	 */
	public function __construct(?Users $currentUser = null) {
		$this->currentUser = $currentUser?:Yii::$app->user->identity;
	}

	/**
	 * @param SellerMiniAssignWithStoreForm $form
	 * @return Sellers
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function assignWithStore(SellerMiniAssignWithStoreForm $form):Sellers {
		if (!$form->validate()) {
			throw new ValidateException($form->getErrors());
		}
		$phone = Phones::defaultFormat($form->phone_number);
		$existentMiniSeller = (new SellersSearch())->findMiniSellerWithPhoneOrEmail($phone, $form->email);
		if (null === $existentMiniSeller) {
			throw new DomainException("Продавец не найден");
		}

		if (!empty($existentMiniSeller->stores)) {
			throw new DomainException("Продавец уже имеет связь с магазином");
		}

		$stores = $this->currentUser->getStoresViaRole();
		$storesKey = ArrayHelper::index($stores, 'id');
		if (!array_key_exists($form->store_id, $storesKey)) {
			throw new DomainException("Вы не можете прикрепить к текущей точке");
		}

		$selectedStore = $storesKey[$form->store_id];
		RelStoresToSellers::linkModel($selectedStore, $existentMiniSeller);

		return $existentMiniSeller;
	}
}
