<?php
declare(strict_types = 1);

namespace app\models\addresses\active_record;

use app\models\regions\active_record\references\RefRegions;
use app\modules\history\behaviors\HistoryBehavior;
use pozitronik\helpers\DateHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "addresses".
 *
 * @property int $id
 * @property string $create_date Дата создания
 * @property int $index Индекс
 * @property int $area Область
 * @property string $region Регион/район
 * @property string $city Город/н.п.
 * @property string $street Улица
 * @property string $building Дом
 * @property int $deleted
 *
 * @property RefRegions $refRegion Область (справочник)
 */
class AddressesAR extends ActiveRecord {
	public const SCENARIO_EDIT_SELLER = 'edit_default';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'addresses';
	}

	/**
	 * {@inheritdoc}
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
	public function rules():array {
		return [
			[['city', 'street', 'building'], 'required', 'on' => self::SCENARIO_EDIT_SELLER],
			['create_date', 'default', 'value' => DateHelper::lcDate()],
			[['index', 'area', 'deleted'], 'integer'],
			[['region', 'city', 'street', 'building'], 'string', 'max' => 255]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'create_date' => 'Дата создания',
			'index' => 'Индекс',
			'area' => 'Область',
			'region' => 'Регион/район',
			'city' => 'Город/н.п.',
			'street' => 'Улица',
			'building' => 'Дом',
			'deleted' => 'Deleted'
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRefRegion():ActiveQuery {
		return $this->hasOne(RefRegions::class, ['id' => 'area']);
	}
}