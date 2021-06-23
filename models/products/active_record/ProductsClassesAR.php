<?php
declare(strict_types = 1);

namespace app\models\products\active_record;

use app\models\core\prototypes\ActiveRecordTrait;
use app\modules\history\behaviors\HistoryBehavior;
use pozitronik\helpers\DateHelper;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property int $name Название товара
 * @property string $item_class Класс товара
 * @property string $create_date Дата регистрации
 * @property int $deleted
 */
class ProductsClassesAR extends ActiveRecord {
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
		return 'products_classes';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['name', 'item_class'], 'required'],
			[['deleted'], 'boolean'],
			[['create_date'], 'default', 'value' => DateHelper::lcDate()],
			[['name', 'item_class'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'name' => 'Название товара',
			'item_class' => 'Класс товара',
			'create_date' => 'Дата регистрации',
			'deleted' => 'Deleted',
		];
	}
}
