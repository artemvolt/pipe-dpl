<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\controllers\SellersController;
use app\models\addresses\active_record\AddressesAR;
use app\models\addresses\Addresses;
use app\models\common\traits\CreateAddressTrait;
use app\models\phones\Phones;
use app\models\seller\active_record\SellersAR;
use app\models\common\traits\CreateAccessTrait;
use app\models\sys\users\Users;
use app\modules\status\models\Status;
use DomainException;
use InvalidArgumentException;
use pozitronik\filestorage\traits\FileStorageTrait;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class Sellers
 * Конкретный продавец
 * @property mixed $passportScan Скан паспорта или его фото
 * @property mixed $passportSelfie Селфи с паспортом
 * @property mixed $passportTranslation Скан нотариально заверенного перевода (в случае если нет транскрипции на
 * кириллицу)
 * @property mixed $migrationCard Миграционная карта (всем, кроме граждан Беларуси)
 * @property mixed $placeOfStay Отрывная часть бланка к уведомлению о прибытии в место пребывания
 * @property mixed $patent Патент + квитанция об оплате
 * @property mixed $residence Вид на жительство
 * @property mixed $temporaryResidence Разрешение на временное проживание
 * @property mixed $visa Виза
 *
 * @property string $urlToEntity
 */
class Sellers extends SellersAR {
	use FileStorageTrait;
	use CreateAccessTrait;
	use CreateAddressTrait;

	public const SELLER_NOT_ACTIVE = 1;
	public const SELLER_ACTIVE = 2;
	public const SELLER_LIMITED = 3;
	public const SELLER_BLOCKED = 4;
	public const SELLER_SUSPENDED = 5;

	public $passportScan;
	public $passportSelfie;
	public $passportTranslation;
	public $migrationCard;
	public $placeOfStay;
	public $patent;
	public $residence;
	public $temporaryResidence;
	public $visa;

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return ArrayHelper::merge(parent::attributeLabels(), [
			'passportScan' => 'Скан паспорта',
			'passportSelfie' => 'Селфи с паспортом',
			'sellerDocs' => 'Сканы',
			'passportTranslation' => 'Скан нотариально заверенного перевода',
			'migrationCard' => 'Миграционная карта',
			'placeOfStay' => 'Отрывная часть бланка к уведомлению о прибытии в место пребывания',
			'patent' => 'Патент + квитанция об оплате',
			'residence' => 'Вид на жительство',
			'visa' => 'Виза',
			'temporaryResidence' => 'Разрешение на временное проживание'
		]);
	}

	/**
	 * URL для нахождения продавца по ID
	 * @return string
	 */
	public function getUrlToEntity():string {
		return SellersController::to('index', ['SellersSearch[id]' => $this->id], true);
	}

	/**
	 * @inheritDoc
	 */
	public function getAddressesInstance():Addresses {
		if (null === $this->addressInstance) {
			$this->addressInstance = new Addresses();
			if (!$this->isNewRecord) {
				$this->addressInstance->scenario = AddressesAR::SCENARIO_EDIT_SELLER;
			}
		}
		return $this->addressInstance;
	}

	/**
	 * @return Phones[]
	 */
	public function getPhonesNumbers():array {
		if (null === $user = $this->relatedUser) {
			return [];
		}

		return $user->relatedPhones;
	}

	/**
	 * @param int $status
	 * @throws InvalidConfigException
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function changeStatus(int $status):void {
		if (!$this->isExistStatus($status)) {
			throw new InvalidArgumentException("Неизвестный статус");
		}
		$isSaved = Status::setCurrentStatus($this, $status);
		if (false === $isSaved) {
			throw new DomainException("Не получилось установить статус для продавца");
		}
	}

	/**
	 * @param int $status
	 * @return bool
	 */
	public function isExistStatus(int $status):bool {
		return array_key_exists($status, array_keys(self::status_config()));
	}

	/**
	 * @return bool
	 * @throws InvalidConfigException
	 */
	public function isUnActiveStatus():bool {
		$existentStatus = Status::getCurrentStatus($this);
		if (null === $existentStatus) {
			return false;
		}
		return $existentStatus === self::SELLER_NOT_ACTIVE;
	}

	/**
	 * @return array[]
	 */
	public static function status_config():array {
		return [
			self::SELLER_NOT_ACTIVE => [
				'id' => self::SELLER_NOT_ACTIVE,
				'name' => 'Не активирован',
				'initial' => true,
				'finishing' => false,
				'next' => [self::SELLER_ACTIVE, self::SELLER_LIMITED, self::SELLER_BLOCKED, self::SELLER_SUSPENDED],
				'allowed' => false
			],
			self::SELLER_ACTIVE => [
				'id' => self::SELLER_ACTIVE,
				'name' => 'Активирован',
				'initial' => false,
				'finishing' => true,
				'next' => [self::SELLER_NOT_ACTIVE, self::SELLER_LIMITED, self::SELLER_BLOCKED, self::SELLER_SUSPENDED],
				'allowed' => static function(Sellers $model, Users $user):bool {
					return true;
				},
				'style' => 'background: #ffa700; color:black'//стили можно задавать напрямую
			],
			self::SELLER_LIMITED => [
				'id' => self::SELLER_LIMITED,
				'name' => 'Ограничен',
				'initial' => false,
				'finishing' => true,
				'next' => [self::SELLER_NOT_ACTIVE, self::SELLER_ACTIVE, self::SELLER_BLOCKED, self::SELLER_SUSPENDED],
				'allowed' => static function(Sellers $model, Users $user):bool {
					return true;
				},
				'color' => '#00ff00'
			],
			self::SELLER_BLOCKED => [
				'id' => self::SELLER_BLOCKED,
				'name' => 'Заблокирован',
				'initial' => false,
				'finishing' => true,
				'next' => [self::SELLER_NOT_ACTIVE, self::SELLER_ACTIVE, self::SELLER_LIMITED, self::SELLER_SUSPENDED],
				'allowed' => static function(Sellers $model, Users $user):bool {
					return true;
				},
				'color' => '#00ff00'
			],
			self::SELLER_SUSPENDED => [
				'id' => self::SELLER_SUSPENDED,
				'name' => 'Suspend',
				'initial' => false,
				'finishing' => true,
				'next' => [self::SELLER_NOT_ACTIVE, self::SELLER_ACTIVE, self::SELLER_LIMITED, self::SELLER_BLOCKED],
				'allowed' => static function(Sellers $model, Users $user):bool {
					return true;
				},
				'color' => '#00ff00'
			]
		];
	}
}