<?php
declare(strict_types = 1);

namespace app\models\seller\active_record;

use app\components\db\ActiveRecordTrait;
use app\models\addresses\Addresses;
use app\models\countries\active_record\references\RefCountries;
use app\models\dealers\active_record\relations\RelDealersToSellers;
use app\models\dealers\Dealers;
use app\models\managers\Managers;
use app\models\phones\PhoneNumberValidator;
use app\models\regions\active_record\references\RefRegions;
use app\models\store\active_record\relations\RelStoresToSellers;
use app\models\store\Stores;
use app\models\sys\permissions\traits\ActiveRecordPermissionsTrait;
use app\models\sys\users\Users;
use app\modules\history\behaviors\HistoryBehavior;
use app\modules\status\models\traits\StatusesTrait;
use pozitronik\helpers\DateHelper;
use Throwable;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\web\JsExpression;

/**
 * This is the model class for table "sellers".
 *
 * @property int $id
 * @property string $name Имя
 * @property string $surname Фамилия
 * @property string $patronymic Отчество
 * @property string $birthday Дата рождения
 * @property string $create_date Дата регистрации
 * @property string $update_date Дата обновления
 * @property int $citizen Гражданство
 * @property string $passport_series Серия паспорта
 * @property string $passport_number Номер паспорта
 * @property string $passport_whom Кем выдан паспорт
 * @property string $passport_when Когда выдан паспорт
 * @property int $gender Пол
 * @property string $reg_address Адрес регистрации/проживания
 * @property string $entry_date Дата въезда в страну
 * @property string $inn ИНН
 * @property string $snils СНИЛС
 * @property string $keyword Ключевое слово для  «Горячей линии»
 * @property int $is_wireman_shpd Монтажник ШПД
 * @property int $user Пользователь
 * @property int $contract_signing_address Адрес подписания договора
 * @property int $deleted
 *
 * @property RefCountries $refCountry Страна (справочник)
 * @property RelStoresToSellers[] $relatedStoresToSellers Связь к промежуточной таблице к продавцам
 * @property Stores[] $stores Магазины продавца
 * @property RelDealersToSellers[] $relatedDealersToSellers Связь от промежуточной таблице дилеров к продавцам
 * @property Dealers[] $dealers Дилеры продавца
 * @property Users $relatedUser Пользователь связанный с продавцом
 * @property Addresses $relAddress Адрес регистрации/проживания связанный с продавцом
 * @property RefRegions $refRegion
 */
class SellersAR extends ActiveRecord {
	use ActiveRecordTrait;
	use StatusesTrait;
	use ActiveRecordPermissionsTrait;

	public $email;
	public $login;

	public const SCENARIO_CREATE = 'create';
	public const SCENARIO_EDIT = 'edit';

	/**
	 * @var null|Users $_updatedRelatedUser Используется для предвалидации пользователя при изменении
	 */
	private ?Users $_updatedRelatedUser = null;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'sellers';
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors():array {
		return [
			'history' => [
				'class' => HistoryBehavior::class
			],
			[
				'class' => TimestampBehavior::class,
				'createdAtAttribute' => 'create_date',
				'updatedAtAttribute' => 'update_date',
				'value' => DateHelper::lcDate(),
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[
				[
					'name', 'surname', 'patronymic', 'keyword', 'birthday', 'entry_date', 'email', 'login',
					'passport_series', 'passport_number', 'passport_whom', 'passport_when', 'inn', 'snils'
				],
				'filter',
				'filter' => 'trim'
			],
			['is_wireman_shpd', 'default', 'value' => 0],
			[
				[
					'inn', 'snils', 'passport_when', 'passport_whom', 'passport_number', 'passport_series',
					'entry_date'
				],
				'default',
				'value' => null
			],
			[['name', 'surname'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_EDIT]],
			[
				['passport_series', 'passport_number', 'passport_whom', 'passport_when', 'birthday', 'inn', 'citizen'],
				'required',
				'on' => self::SCENARIO_EDIT
			],
			[
				[
					'name', 'surname', 'passport_series', 'passport_number', 'passport_whom',
					'passport_when', 'birthday', 'inn', 'citizen'
				],
				'required',
				'on' => [self::SCENARIO_CREATE, self::SCENARIO_EDIT]
			],
			[['email', 'login',], 'required', 'on' => self::SCENARIO_CREATE],
			['email', 'email', 'on' => self::SCENARIO_CREATE],
			[
				'email',
				function(string $attribute):void {
					if (null !== Users::findByEmail($this->email)) {
						$this->addError('email', 'Пользователь с таким почтовым адресом уже зарегистрирован');
					}
				},
				'on' => self::SCENARIO_CREATE
			],
			[
				'login',
				function(string $attribute):void {
					if (null !== Users::findByLogin($this->login)) {
						$this->addError('login', 'Такой логин уже занят');
					}
				},
				'on' => self::SCENARIO_CREATE
			],
			['login', PhoneNumberValidator::class, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_EDIT]],
			[['create_date', 'update_date', 'stores', 'dealers', 'currentStatusId'], 'safe'],
			[['passport_when', 'birthday', 'entry_date'], 'date', 'format' => 'php:Y-m-d'],
			[['patronymic'], 'default', 'value' => null],
			[
				'entry_date',
				'required',
				'when' => static function($model) {
					/** @var self $model */
					return 0 === (null !== $model->refCountry?$model->refCountry->is_homeland:1);
				},
				'whenClient' => new JsExpression("function() {
					return isForeigner([".
					implode(',', ArrayHelper::getColumn(RefCountries::getHomelandCountries(), 'id', [])).
					"], document.getElementById('sellers-citizen').value)
					}"),
				'on' => [self::SCENARIO_CREATE, self::SCENARIO_EDIT]
			],
			[
				[
					'gender', 'is_wireman_shpd', 'deleted', 'user', 'inn', 'citizen',
					'reg_address'
				],
				'integer',
				'on' => [self::SCENARIO_CREATE, self::SCENARIO_EDIT]
			],
			[['name', 'surname', 'patronymic'], 'string', 'max' => 128],
			[['passport_series', 'passport_number', 'keyword', 'login'], 'string', 'max' => 64],
			[['passport_whom', 'email', 'contract_signing_address'], 'string', 'max' => 255],
			['inn', 'string', 'length' => 12, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_EDIT]],
			[
				'snils',
				'match',
				'pattern' => '/^(\d{3}\-\d{3}-\d{3} \d{2})$/',
				'message' => 'Значение «СНИЛС» неверно. Формат: 000-000-000 00',
				'on' => [self::SCENARIO_CREATE, self::SCENARIO_EDIT]
			],
			[['inn', 'snils', 'user'], 'unique', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_EDIT]],
			[
				['passport_series', 'passport_number'],
				'unique',
				'targetAttribute' => ['passport_series', 'passport_number'],
				'on' => [self::SCENARIO_CREATE, self::SCENARIO_EDIT]
			],
			['relatedUser', function(string $attribute) {
				/*не соображу, как доделать. Пока не критично, отложил*/
				if (null !== $this->_updatedRelatedUser) {
					$this->addError('relatedUser', 'Этот аккаунт уже привязан к другому продавцу');
				}
			}, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_EDIT]],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'currentStatusId' => 'Статус',
			'user' => 'Пользователь',
			'name' => 'Имя',
			'surname' => 'Фамилия',
			'patronymic' => 'Отчество',
			'birthday' => 'Дата рождения',
			'userEmail' => 'Почта',
			'userId' => 'Ид пользователя',
			'userLogin' => 'Логин',
			'create_date' => 'Дата регистрации',
			'update_date' => 'Дата обновления',
			'citizen' => 'Гражданство',
			'passport' => 'Паспорт',
			'passport_series' => 'Серия паспорта',
			'passport_number' => 'Номер паспорта',
			'passport_whom' => 'Кем выдан паспорт',
			'passport_when' => 'Когда выдан паспорт',
			'gender' => 'Пол',
			'reg_address' => 'Адрес регистрации/проживания',
			'relAddress' => 'Адрес регистрации/проживания',
			'entry_date' => 'Дата въезда в страну',
			'inn' => 'ИНН',
			'snils' => 'СНИЛС',
			'keyword' => 'Ключевое слово для  «Горячей линии»',
			'is_wireman_shpd' => 'Монтажник ШПД',
			'contract_signing_address' => 'Адрес подписания договора',
			'stores' => 'Магазин',
			'dealers' => 'Дилер',
			'deleted' => 'Deleted'
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedStoresToSellers():ActiveQuery {
		return $this->hasMany(RelStoresToSellers::class, ['seller_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getStores():ActiveQuery {
		return $this->hasMany(Stores::class, ['id' => 'store_id'])->via('relatedStoresToSellers');
	}

	/**
	 * @param mixed $stores
	 * @throws Throwable
	 */
	public function setStores($stores):void {
		RelStoresToSellers::linkModels($stores, $this, true);/* Соединение идёт "наоборот", добавляем ключ backLink */
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedDealersToSellers():ActiveQuery {
		return $this->hasMany(RelDealersToSellers::class, ['seller_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getDealers():ActiveQuery {
		return $this->hasMany(Dealers::class, ['id' => 'dealer_id'])->via('relatedDealersToSellers');
	}

	/**
	 * @param mixed $dealers
	 * @throws Throwable
	 */
	public function setDealers($dealers):void {
		RelDealersToSellers::linkModels($dealers, $this, true);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedUser():ActiveQuery {
		return $this->hasOne(Users::class, ['id' => 'user']);
	}

	/**
	 * @param mixed $relatedUser
	 */
	public function setRelatedUser($relatedUser):void {
		/** @var Users $relatedUser */
		if ((null !== $relatedUser = self::ensureModel(Users::class, $relatedUser)) && 0 !== (int)self::find()->where(['user' => $relatedUser->id])->andWhere(['not in', 'id', $this->id])->count()) {
			/* Этот пользователь уже привязан к другому продавцу.
			 * Возможно, мы захотим прописать логику "отвяжи там, привяжи тут", пока оставим так.
			 *
			 * link() не делает валидацию.
			 * $this->_updatedRelatedUser проверится в валидаторе на save().
			 *
			 * Другой вариант - делать связь через релейшен-таблицу со своей валидацией.
			 */
			$this->_updatedRelatedUser = $relatedUser;
			return;
		}

		$this->link('relatedUser', $relatedUser);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRefCountry():ActiveQuery {
		return $this->hasOne(RefCountries::class, ['id' => 'citizen']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelAddress():ActiveQuery {
		return $this->hasOne(Addresses::class, ['id' => 'reg_address']);
	}

	/**
	 * @param mixed $relAddress
	 */
	public function setRelAddress($relAddress):void {
		/** @var Addresses $relAddress */
		$this->link('relAddress', $relAddress);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRefRegion():ActiveQuery {
		return $this->hasOne(RefRegions::class, ['id' => 'area'])->via('relAddress');
	}

	/**
	 * @inheritDoc
	 */
	public static function scope(ActiveQueryInterface $query, IdentityInterface $user):ActiveQueryInterface {
		/** @var Users $user */
		if ($user->isAllPermissionsGranted()) return $query;
		if ($user->hasPermission(['show_all_sellers'])) return $query;

		$query->where([self::tableName().'.id' => '0']);

		if (null !== $manager = Managers::findOne(['user' => $user->id])) {
			if ($user->hasPermission(['dealer_sellers'])) {
				$query->joinWith(['dealers']);
				$query->orWhere([Dealers::tableName().'.id' => $manager->getRelatedDealersToManagers()->select('dealer_id')]);
			}
			if ($user->hasPermission(['dealer_store_sellers'])) {
				$query->joinWith(['stores']);
				$query->orWhere([Stores::tableName().'.id' => $manager->getRelatedManagersToStores()->select('store_id')]);
			}
		}
		return $query;
	}
}
