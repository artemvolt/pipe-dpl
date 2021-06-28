<?php
declare(strict_types = 1);

namespace app\models\sys\users;

use app\components\db\ActiveRecordTrait;
use app\models\sys\users\active_record\UsersTokens as ActiveRecordUsersTokens;
use Exception;
use pozitronik\helpers\DateHelper;
use yii\behaviors\TimestampBehavior;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\helpers\ArrayHelper;

/**
 * Class UsersTokens
 * @package app\models\sys\users
 */
class UsersTokens extends ActiveRecordUsersTokens {
	use ActiveRecordTrait;

	/**
	 * Массив соответствий между методом авторизации и типом токена.
	 * @see Users::findIdentityByAccessToken()
	 */
	public const TOKEN_TYPES = [
		HttpHeaderAuth::class => 1,
		HttpBearerAuth::class => 2
	];

	/**
	 * {@inheritdoc}
	 */
	public function behaviors():array {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => TimestampBehavior::class,
				'createdAtAttribute' => 'created',
				'updatedAtAttribute' => 'created',
				'value' => static function($event) {
					return DateHelper::from_unix_timestamp(time());
				}
			]
		]);
	}

	/**
	 * Проверяем актуальность ключа доступа.
	 * @return bool
	 */
	public function isValid():bool {
		return (null === $this->valid) || DateHelper::unix_timestamp($this->valid) > time();
	}

	/**
	 * @param null|string $type
	 * @return int|null
	 * @throws Exception
	 */
	public static function getIdByType(?string $type):?int {
		return ArrayHelper::getValue(self::TOKEN_TYPES, $type);
	}
}