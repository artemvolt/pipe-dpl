<?php
declare(strict_types = 1);

namespace app\modules\import\models\active_record;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $model
 * @property int $domain
 * @property resource $data
 * @property int $processed
 */
class Import extends ActiveRecord {
	public const NOT_PROCESSED = 0;
	public const PROCESSED = 1;
	public const PROCESSED_ERROR = 2;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'sys_import';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['model', 'domain'], 'required'],
			[['domain'], 'integer'],
			[['processed'], 'integer'],
			[['data'], 'string'],
			[['model'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'model' => 'Model',
			'domain' => 'Domain',
			'data' => 'Data',
			'processed' => 'Processed'
		];
	}
}
