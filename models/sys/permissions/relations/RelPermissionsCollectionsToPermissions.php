<?php
declare(strict_types = 1);

namespace app\models\sys\permissions\relations;

use app\models\sys\permissions\PermissionsAR;
use app\models\sys\permissions\PermissionsCollectionsAR;
use pozitronik\core\traits\Relations;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sys_relation_permissions_collections_to_permissions".
 *
 * @property int $id
 * @property int $collection_id Ключ группы доступа
 * @property int $permission_id Ключ правила доступа
 *
 * @property PermissionsCollectionsAR $relatedPermissionsCollections Связанная группа доступов
 * @property PermissionsAR $relatedPermissions Связанный доступ
 */
class RelPermissionsCollectionsToPermissions extends ActiveRecord {
	use Relations;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'sys_relation_permissions_collections_to_permissions';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['collection_id', 'permission_id'], 'required'],
			[['collection_id', 'permission_id'], 'integer'],
			[['collection_id', 'permission_id'], 'unique', 'targetAttribute' => ['collection_id', 'permission_id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'collection_id' => 'Collection ID',
			'permission_id' => 'Permission ID',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedPermissionsCollections():ActiveQuery {
		return $this->hasMany(PermissionsCollectionsAR::class, ['id' => 'collection_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedPermissions():ActiveQuery {
		return $this->hasMany(PermissionsAR::class, ['id' => 'permission_id']);
	}
}
