<?php
declare(strict_types = 1);

namespace app\models\seller\active_record;

use app\models\store\Stores;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sellers_invite_links".
 *
 * @property int $id
 * @property int $store_id
 * @property int $phone_number
 * @property string $email
 * @property string $token
 * @property string $expired_at
 *
 * @property Stores $store
 */
class SellerInviteLinkAr extends ActiveRecord {
	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'sellers_invite_links';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['store_id', 'phone_number'], 'integer'],
			[['token'], 'required'],
			[['expired_at'], 'safe'],
			[['email', 'token'], 'string', 'max' => 255],
			[['store_id', 'phone_number', 'email'], 'unique', 'targetAttribute' => ['store_id', 'phone_number', 'email']],
			[['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::class, 'targetAttribute' => ['store_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'store_id' => 'Store ID',
			'phone_number' => 'Phone Number',
			'email' => 'Email',
			'token' => 'Token',
			'expired_at' => 'Expired At',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getStore():ActiveQuery {
		return $this->hasOne(Stores::class, ['id' => 'store_id']);
	}
}
