<?php
declare(strict_types = 1);

namespace app\models\store\active_record\relations;

use pozitronik\core\traits\Relations;
use yii\db\ActiveRecord;

/**
 *
 * Связь магазинов с пользователями
 * @property int $id
 * @property int $store_id
 * @property int $user_id
 */
class RelStoresToUsers extends ActiveRecord {
	use Relations;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'relation_stores_to_users';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['store_id', 'user_id'], 'required'],
			[['store_id', 'user_id'], 'integer'],
			[['store_id', 'user_id'], 'unique', 'targetAttribute' => ['store_id', 'user_id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'store_id' => 'Store ID',
			'user_id' => 'User ID',
		];
	}
}
