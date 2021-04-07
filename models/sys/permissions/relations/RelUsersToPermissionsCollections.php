<?php
declare(strict_types = 1);

namespace app\models\sys\permissions\relations;

use app\models\sys\permissions\PermissionsCollectionsAR;
use app\models\sys\users\Users;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sys_relation_users_to_permissions_collections".
 *
 * @property int $id
 * @property int $user_id Ключ объекта доступа
 * @property int $collection_id Ключ группы доступа
 *
 * @property null|Users $relatedUsers Связанная модель пользователя
 * @property null|PermissionsCollectionsAR $relatedPermissionsCollections Связанная группа доступа
 */
class RelUsersToPermissionsCollections extends ActiveRecord {
	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'sys_relation_users_to_permissions_collections';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['user_id', 'collection_id'], 'required'],
			[['user_id', 'collection_id'], 'integer'],
			[['user_id', 'collection_id'], 'unique', 'targetAttribute' => ['user_id', 'collection_id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'user_id' => 'User ID',
			'collection_id' => 'Collection ID',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedUsers():ActiveQuery {
		return $this->hasOne(Users::class, ['id' => 'user_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedPermissionsCollections():ActiveQuery {
		return $this->hasOne(PermissionsCollectionsAR::class, ['id' => 'collection_id']);
	}

}
