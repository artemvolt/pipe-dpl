<?php
declare(strict_types = 1);

namespace app\models\managers\active_record;

use app\components\db\ActiveRecordTrait;
use app\models\dealers\active_record\relations\RelDealersToManagers;
use app\models\dealers\Dealers;
use app\models\managers\active_record\relations\RelManagersToStores;
use app\models\phones\PhoneNumberValidator;
use app\models\store\Stores;
use app\models\sys\permissions\traits\ActiveRecordPermissionsTrait;
use app\models\sys\users\Users;
use app\modules\history\behaviors\HistoryBehavior;
use pozitronik\helpers\DateHelper;
use Throwable;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "managers".
 *
 * @property int $id
 * @property string $create_date Дата регистрации
 * @property string $update_date Дата обновления
 * @property int $user Пользователь
 * @property string $name Имя
 * @property string $surname Фамилия
 * @property string $patronymic Отчество
 * @property int $deleted
 *
 * @property string $email
 * @property string $login
 *
 * @property RelManagersToStores[] $relatedManagersToStores Связь к промежуточной таблице к магазинам
 * @property Stores[] $stores Магазины менеджера
 * @property RelDealersToManagers[] $relatedDealersToManagers Связь к промежуточной таблице к дилерам
 * @property Dealers[] $dealers Дилеры менеджера
 * @property Users $relatedUser Пользователь связанный с менеджером
 */
class ManagersAR extends ActiveRecord {
	use ActiveRecordTrait;
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
		return 'managers';
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
			[['email', 'login', 'name', 'surname', 'patronymic'], 'filter', 'filter' => 'trim'],
			[['name', 'surname'], 'required'],
			[['name', 'surname'], 'string', 'max' => 128],
			[['email', 'login',], 'required', 'on' => self::SCENARIO_CREATE],
			['email', 'string', 'max' => 255],
			['login', 'string', 'max' => 64],
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
			['login', PhoneNumberValidator::class],
			[['create_date', 'update_date', 'stores', 'dealers'], 'safe'],
			[['deleted', 'user'], 'integer'],
			['user', 'unique']
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'create_date' => 'Дата регистрации',
			'update_date' => 'Дата обновления',
			'user' => 'Пользователь',
			'userEmail' => 'Почта',
			'userId' => 'Ид пользователя',
			'userLogin' => 'Логин',
			'name' => 'Имя',
			'surname' => 'Фамилия',
			'patronymic' => 'Отчество',
			'stores' => 'Магазины',
			'dealers' => 'Дилеры',
			'deleted' => 'Deleted'
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedManagersToStores():ActiveQuery {
		return $this->hasMany(RelManagersToStores::class, ['manager_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getStores():ActiveQuery {
		return $this->hasMany(Stores::class, ['id' => 'store_id'])->via('relatedManagersToStores');
	}

	/**
	 * @param mixed $stores
	 * @throws Throwable
	 */
	public function setStores($stores):void {
		RelManagersToStores::linkModels($this, $stores);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedDealersToManagers():ActiveQuery {
		return $this->hasMany(RelDealersToManagers::class, ['manager_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getDealers():ActiveQuery {
		return $this->hasMany(Dealers::class, ['id' => 'dealer_id'])->via('relatedDealersToManagers');
	}

	/**
	 * @param mixed $dealers
	 * @throws Throwable
	 */
	public function setDealers($dealers):void {
		RelDealersToManagers::linkModels($dealers, $this, true);
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
		if (
			(null !== $relatedUser = self::ensureModel(Users::class, $relatedUser))
			&& 0 !== (int)self::find()->where(['user' => $relatedUser->id])->andWhere(['not in', 'id', $this->id])->count()
		) {
			/* Этот пользователь уже привязан к другому менеджеру.
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
	 * @inheritDoc
	 */
	public static function scope(ActiveQueryInterface $query, Users $user):ActiveQueryInterface {
		if ($user->isAllPermissionsGranted()) return $query;
		if ($user->hasPermission(['show_all_managers'])) return $query;

		$query->where([self::tableName().'.id' => '0']);//пользователь получает сасай

		if ((null !== $manager = self::findOne(['user' => $user->id])) && $user->hasPermission(['manager_dealer'])) {
			$query->joinWith(['dealers']);
			return $query->orWhere([Dealers::tableName().'.id' => $manager->getRelatedDealersToManagers()->select('dealer_id')]);
		}

		return $query;
	}
}
