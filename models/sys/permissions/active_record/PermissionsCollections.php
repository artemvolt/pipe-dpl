<?php
declare(strict_types = 1);

namespace app\models\sys\permissions\active_record;

use app\components\db\ActiveRecordTrait;
use app\models\sys\permissions\active_record\relations\RelPermissionsCollectionsToPermissions;
use app\models\sys\permissions\active_record\relations\RelUsersToPermissionsCollections;
use app\models\sys\users\Users;
use app\modules\history\behaviors\HistoryBehavior;
use pozitronik\helpers\ArrayHelper;
use Throwable;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sys_permissions_collections".
 *
 * @property int $id
 * @property string|null $name Название группы доступа
 * @property string|null $comment Описание группы доступа
 *
 * @property RelPermissionsCollectionsToPermissions[] $relatedPermissionsCollectionsToPermissions Связь к промежуточной таблице к правам доступа
 * @property RelUsersToPermissionsCollections[] $relatedUsersToPermissionsCollections Связь к промежуточной таблице к пользователям
 * @property Permissions[] $relatedPermissions Входящие в группу доступа права доступа
 * @property Users[] $relatedUsers Все пользователи, у которых есть эта группа доступа
 * @property-read Permissions[] $unrelatedPermissions Права доступа, которые не включены в набор
 */
class PermissionsCollections extends ActiveRecord {
	use ActiveRecordTrait;

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
		return 'sys_permissions_collections';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['comment'], 'string'],
			[['name'], 'string', 'max' => 128],
			[['name'], 'unique'],
			[['name'], 'required'],
			[['relatedPermissions', 'relatedUsers'], 'safe']
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'name' => 'Название',
			'comment' => 'Комментарий'
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedUsersToPermissionsCollections():ActiveQuery {
		return $this->hasMany(RelUsersToPermissionsCollections::class, ['collection_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedPermissionsCollectionsToPermissions():ActiveQuery {
		return $this->hasMany(RelPermissionsCollectionsToPermissions::class, ['collection_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedPermissions():ActiveQuery {
		return $this->hasMany(Permissions::class, ['id' => 'permission_id'])->via('relatedPermissionsCollectionsToPermissions');
	}

	/**
	 * @param mixed $relatedPermissions
	 * @throws Throwable
	 */
	public function setRelatedPermissions($relatedPermissions):void {
		if (empty($relatedPermissions)) {
			RelPermissionsCollectionsToPermissions::clearLinks($this);
		} else {
			RelPermissionsCollectionsToPermissions::linkModels($this, $relatedPermissions);
		}
	}

	/**
	 * @return Permissions[]
	 */
	public function getUnrelatedPermissions():array {
		return Permissions::find()->where(['not in', 'id', ArrayHelper::getColumn($this->relatedPermissions, 'id')])->all();
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedUsers():ActiveQuery {
		return $this->hasMany(Users::class, ['id' => 'user_id'])->via('relatedUsersToPermissionsCollections');
	}

}
