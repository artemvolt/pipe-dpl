<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\components\exceptions\MuteManager;
use app\components\exceptions\ValidateException;
use app\models\phones\Phones;
use app\models\seller\invite_link\CreateSellerInviteLinkForm;
use app\models\seller\invite_link\notification\EmailNotification;
use app\models\seller\invite_link\notification\SmsNotification;
use app\models\store\active_record\relations\RelStoresToSellers;
use app\models\sys\users\Users;
use DomainException;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\mail\MailerInterface;

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
	 * @var SmsNotification $smsNotification
	 */
	protected $smsNotification;
	/**
	 * @var MuteManager $muteManager
	 */
	protected $mute;
	/**
	 * @var MailerInterface $emailNotification
	 */
	protected $emailNotification;

	/**
	 * SellerMiniService constructor.
	 */
	public function __construct() {
		$this->currentUser = Yii::$app->user->identity;
		$this->smsNotification = new SmsNotification();
		$this->emailNotification = new EmailNotification();
		$this->mute = new MuteManager();
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

	/**
	 * @param CreateSellerInviteLinkForm $form
	 * @return SellerInviteLink
	 * @throws Throwable
	 * @throws ValidateException
	 * @throws Exception
	 */
	public function createInviteLink(CreateSellerInviteLinkForm $form):SellerInviteLink {
		if (!$form->validate()) {
			throw new ValidateException($form->getErrors());
		}

		$link = SellerInviteLink::createLink((int)$form->store_id, $form->phone_number, $form->email);

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
			$this->emailNotification->notify($email, $url);
		});
	}

	/**
	 * @param string $phone
	 * @param string $url
	 * @throws Throwable
	 */
	protected function sendSms(string $phone, string $url):void {
		$this->mute->mute(function() use ($phone, $url) {
			$this->smsNotification->notify($phone, $url);
		});
	}
}
