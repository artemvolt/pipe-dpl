<?php
declare(strict_types = 1);

namespace app\models\sys\users\active_record;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sys_users_tokens".
 *
 * @property int $id
 * @property int $user_id user id foreign key
 * @property string $auth_token Bearer auth token
 * @property int $type_id Тип токена
 * @property string $created Таймстамп создания
 * @property string|null $valid Действует до, null - бессрочно. Сейчас не используется, но можно сделать принудительный рефреш истекшего токена.
 * @property string|null $ip Адрес авторизации
 * @property string|null $user_agent User-Agent
 *
 * @property null|Users $relatedUsers
 */
class UsersTokens extends ActiveRecord {
	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'sys_users_tokens';
	}

	/**
	 * @return string
	 */
	private static function GenerateToken():string {
		return sha1(uniqid((string)mt_rand(), true));
	}

	/**
	 * @inheritDoc
	 */
	public function beforeValidate():bool {
		if ($this->isNewRecord && (null === $this->auth_token)) {
			$this->auth_token = self::GenerateToken();
		}
		return parent::beforeValidate();
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['user_id', 'auth_token', 'type_id'], 'required'],
			[['user_id', 'type_id'], 'integer'],
			[['created', 'valid'], 'safe'],
			[['auth_token'], 'string', 'max' => 40],
			[['ip', 'user_agent'], 'string', 'max' => 255],
			[['user_id', 'auth_token'], 'unique', 'targetAttribute' => ['user_id', 'auth_token']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'user_id' => 'user id foreign key',
			'auth_token' => 'Bearer auth token',
			'type_id' => 'Тип токена',
			'created' => 'Время создания',
			'valid' => 'Действует до',
			'ip' => 'Адрес авторизации',
			'user_agent' => 'User-Agent',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedUsers():ActiveQuery {
		return $this->hasOne(Users::class, ['id' => 'user_id']);
	}
}
