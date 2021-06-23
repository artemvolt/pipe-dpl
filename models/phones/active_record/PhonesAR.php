<?php
declare(strict_types = 1);

namespace app\models\phones\active_record;

use pozitronik\helpers\DateHelper;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phones".
 *
 * @property int $id
 * @property string $phone Телефон
 * @property string $create_date Дата регистрации
 * @property int $status Статус
 * @property int $deleted
 */
class PhonesAR extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'phones';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['phone'], 'required'],
			[['create_date'], 'safe'],
			[['status', 'deleted'], 'integer'],
			[['phone'], 'string', 'max' => 255],
			[['create_date'], 'default', 'value' => DateHelper::lcDate()]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'phone' => 'Телефон',
			'create_date' => 'Дата регистрации',
			'status' => 'Статус',
			'deleted' => 'Deleted',
		];
	}
}
