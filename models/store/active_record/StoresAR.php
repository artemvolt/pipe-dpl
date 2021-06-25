<?php
declare(strict_types = 1);

namespace app\models\store\active_record;

use app\models\branches\active_record\references\RefBranches;
use app\components\db\ActiveRecordTrait;
use app\models\dealers\active_record\relations\RelDealersToStores;
use app\models\dealers\Dealers;
use app\models\managers\active_record\relations\RelManagersToStores;
use app\models\managers\Managers;
use app\models\regions\active_record\references\RefRegions;
use app\models\seller\Sellers;
use app\models\store\active_record\references\RefSellingChannels;
use app\models\store\active_record\references\RefStoresTypes;
use app\models\store\active_record\relations\RelStoresToSellers;
use app\models\store\active_record\relations\RelStoresToUsers;
use app\models\sys\users\Users;
use pozitronik\helpers\DateHelper;
use Throwable;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stores".
 *
 * @property int $id
 * @property string $name Название магазина
 * @property int $type Тип магазина
 * @property int $branch Филиал
 * @property int $region Регион
 * @property int $selling_channel Тип магазина
 * @property string $create_date Дата регистрации
 * @property int $deleted
 *
 * @property RefStoresTypes $refStoresTypes Тип точки (справочник)
 * @property RefSellingChannels $refSellingChannels Канал продаж (справочник)
 * @property RefBranches $refBranches Филиал (справочник)
 * @property RefRegions $refRegions Регионы (справочник)
 * @property RelStoresToSellers[] $relatedStoresToSellers Связь к промежуточной таблице к продавцам
 * @property Sellers[] $sellers Все продавцы точки
 * @property RelManagersToStores[] $relatedManagersToStores Связь к промежуточной таблице к менеджерам
 * @property Managers[] $managers Все менеджеры точки
 * @property RelDealersToStores $relatedDealersToStores Связь к промежуточной таблице к дилерам
 * @property Dealers $dealer Дилер магазина
 * @property RelStoresToUsers[] $relatedStoresToUsers Связь к промежуточной таблице к пользователям
 * @property Users[] $users Пользователи, входящие под магазином
 */
class StoresAR extends ActiveRecord {
	use ActiveRecordTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'stores';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['name', 'type', 'selling_channel', 'branch', 'region'], 'required'],
			[['type', 'deleted', 'selling_channel', 'branch', 'region'], 'integer'],
			[['create_date', 'sellers', 'managers', 'dealer'], 'safe'],
			[['create_date'], 'default', 'value' => DateHelper::lcDate()],
			[['name'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'name' => 'Название магазина',
			'type' => 'Тип магазина',
			'create_date' => 'Дата регистрации',
			'selling_channel' => 'Канал продаж',
			'branch' => 'Филиал',
			'region' => 'Регион',
			'sellers' => 'Продавцы',
			'managers' => 'Менеджеры',
			'dealer' => 'Дилер',
			'deleted' => 'Deleted',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRefStoresTypes():ActiveQuery {
		return $this->hasOne(RefStoresTypes::class, ['id' => 'type']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedManagersToStores():ActiveQuery {
		return $this->hasMany(RelManagersToStores::class, ['store_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getManagers():ActiveQuery {
		return $this->hasMany(Managers::class, ['id' => 'manager_id'])->via('relatedManagersToStores');
	}

	/**
	 * @param mixed $managers
	 * @throws Throwable
	 */
	public function setManagers($managers):void {
		RelManagersToStores::linkModels($managers, $this, true);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedDealersToStores():ActiveQuery {
		return $this->hasOne(RelDealersToStores::class, ['store_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getDealer():ActiveQuery {
		return $this->hasOne(Dealers::class, ['id' => 'dealer_id'])->via('relatedDealersToStores');
	}

	/**
	 * @param mixed $dealer
	 * @throws Throwable
	 */
	public function setDealer($dealer):void {
		RelDealersToStores::linkModels($dealer, $this, true);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedStoresToSellers():ActiveQuery {
		return $this->hasMany(RelStoresToSellers::class, ['store_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getSellers():ActiveQuery {
		return $this->hasMany(Sellers::class, ['id' => 'seller_id'])->via('relatedStoresToSellers');
	}

	/**
	 * @param mixed $sellers
	 * @throws Throwable
	 */
	public function setSellers($sellers):void {
		RelStoresToSellers::linkModels($this, $sellers);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedStoresToUsers():ActiveQuery {
		return $this->hasMany(RelStoresToUsers::class, ['store_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getUsers():ActiveQuery {
		return $this->hasMany(Users::class, ['id' => 'user_id'])->via('relatedStoresToUsers');
	}

	/**
	 * @param mixed $users
	 * @throws Throwable
	 */
	public function setUsers(array $users):void {
		RelStoresToUsers::linkModels($this, $users);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRefSellingChannels():ActiveQuery {
		return $this->hasOne(RefSellingChannels::class, ['id' => 'selling_channel']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRefBranches():ActiveQuery {
		return $this->hasOne(RefBranches::class, ['id' => 'branch']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRefRegions():ActiveQuery {
		return $this->hasOne(RefRegions::class, ['id' => 'region']);
	}

}
