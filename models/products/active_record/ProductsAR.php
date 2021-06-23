<?php
declare(strict_types = 1);

namespace app\models\products\active_record;

use app\models\core\prototypes\ActiveRecordTrait;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property int $class_id Класс продукта
 * @property int $user Пользователь
 * @property string $create_date Дата создания
 * @property int $deleted
 */
class ProductsAR extends ActiveRecord {
	use ActiveRecordTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'products';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['class_id'], 'required'],
			[['class_id', 'user', 'deleted'], 'integer'],
			[['create_date'], 'safe'],
			[['class_id'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'class_id' => 'Класс продукта',
			'user' => 'Пользователь',
			'create_date' => 'Дата создания',
			'deleted' => 'Deleted',
		];
	}
}
