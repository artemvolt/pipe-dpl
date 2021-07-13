<?php
declare(strict_types = 1);

namespace app\models\products\active_record;

use app\components\db\ActiveRecordTrait;
use app\modules\history\behaviors\HistoryBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "simcard".
 *
 * @property int $id
 * @property int $ICCID
 * @property bool $active
 */
class SimCardAR extends ActiveRecord {
	use ActiveRecordTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'simcard';
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
			[['ICCID'], 'required'],
			[['ICCID'], 'integer'],
			[['active'], 'boolean']
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'ICCID' => 'Iccid',
			'active' => 'Active',
		];
	}
}
