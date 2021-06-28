<?php
declare(strict_types = 1);

namespace app\models\sys\users\active_record\relations;

use pozitronik\relations\traits\RelationsTrait;
use yii\db\ActiveRecord;

/**
 *
 * Связь пользователей с телефонными номерами
 * @property int $id
 * @property int $user_id
 * @property int $phone_id
 */
class RelUsersToPhones extends ActiveRecord {
	use RelationsTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'relation_users_to_phones';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['user_id', 'phone_id'], 'required'],
			[['user_id', 'phone_id'], 'integer'],
			[['user_id', 'phone_id'], 'unique', 'targetAttribute' => ['user_id', 'phone_id']],
			[['phone_id'], 'unique'] /*у разных юзеров не может быть одного телефона*/
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'user_id' => 'User ID',
			'phone_id' => 'Phone ID',
		];
	}
}
