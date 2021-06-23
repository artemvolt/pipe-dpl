<?php
declare(strict_types = 1);

namespace app\models\rest;

use simialbi\yii2\rest\ActiveRecord;

/**
 * Class Users
 * @property string $USER [char(32)]
 * @property int $CURRENT_CONNECTIONS [bigint]
 * @property int $TOTAL_CONNECTIONS [bigint]
 */
class Users extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function modelName():string {
		return 'users';
	}

	/**
	 * {@inheritdoc}
	 */
	public static function primaryKey():array {
		return ['id'];
	}
}