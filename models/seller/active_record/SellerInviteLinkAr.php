<?php
declare(strict_types = 1);

namespace app\models\seller\active_record;

use app\components\db\ActiveRecordTrait;
use app\models\phones\PhoneNumberValidator;
use app\models\store\Stores;
use app\models\sys\permissions\traits\ActiveRecordPermissionsTrait;
use app\modules\history\behaviors\HistoryBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sellers_invite_links".
 *
 * @property int $id
 * @property int $store_id
 * @property string $phone_number
 * @property string $email
 * @property string $token
 * @property string $expired_at
 *
 * @property Stores $store
 */
class SellerInviteLinkAr extends ActiveRecord {
	use ActiveRecordTrait;
	use ActiveRecordPermissionsTrait;

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
		return 'sellers_invite_links';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['store_id'], 'integer'],
			[['store_id', 'token'], 'required'],
			[['expired_at'], 'safe'],
			['phone_number', PhoneNumberValidator::class],
			[['phone_number', 'email', 'token'], 'string', 'max' => 255],
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
			'store_id' => 'ID Магазина',
			'phone_number' => 'Номер телефона',
			'email' => 'Email',
			'token' => 'Токен',
			'expired_at' => 'Действует до',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getStore():ActiveQuery {
		return $this->hasOne(Stores::class, ['id' => 'store_id']);
	}
}
