<?php
declare(strict_types = 1);

namespace app\models\dealers\active_record;

use app\models\core\prototypes\ActiveRecordTrait;
use app\models\branches\active_record\references\RefBranches;
use app\models\dealers\active_record\references\RefDealersGroups;
use app\models\dealers\active_record\references\RefDealersTypes;
use app\modules\history\behaviors\HistoryBehavior;
use pozitronik\helpers\DateHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "dealers".
 *
 * @property int $id
 * @property string $name Название дилера
 * @property string $code Код дилера
 * @property string $client_code Код клиента
 * @property int $group Группа
 * @property int $branch Филиал
 * @property int $type Тип
 * @property string $create_date Дата регистрации
 * @property int $daddy ID зарегистрировавшего/проверившего пользователя
 * @property int $deleted
 *
 * @property RefDealersTypes $refDealersTypes Справочник типов
 * @property RefDealersGroups $refDealersGroups Справочник групп дилеров
 * @property RefBranches $refBranches Справочник филиалов
 */
class DealersAR extends ActiveRecord {
	use ActiveRecordTrait;

	/**
	 * @inheritDoc
	 */
	public function behaviors():array {
		return [
			'history' => [
				'class' => HistoryBehavior::class
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'dealers';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['name', 'code', 'client_code', 'group', 'branch'], 'required'],
			[['group', 'branch', 'type', 'daddy', 'deleted'], 'integer'],
			[['create_date'], 'safe'],
			[['create_date'], 'default', 'value' => DateHelper::lcDate()],/*если делаем так, то не ставим required*/
			[['name'], 'string', 'max' => 255],
			[['code'], 'string', 'max' => 4],
			[['client_code'], 'string', 'max' => 9],
			[['code'], 'unique'],
			[['client_code'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'name' => 'Название дилера',
			'code' => 'Код дилера',
			'client_code' => 'Код клиента',
			'group' => 'Группа',
			'branch' => 'Филиал',
			'type' => 'Тип',
			'create_date' => 'Дата регистрации',
			'daddy' => 'ID зарегистрировавшего/проверившего пользователя',
			'deleted' => 'Deleted'
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRefDealersTypes():ActiveQuery {
		return $this->hasOne(RefDealersTypes::class, ['id' => 'type']);
	}

	/**
	 * @returnActiveQuery
	 */
	public function getRefDealersGroups():ActiveQuery {
		return $this->hasOne(RefDealersGroups::class, ['id' => 'group']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRefBranches():ActiveQuery {
		return $this->hasOne(RefBranches::class, ['id' => 'branch']);
	}
}
