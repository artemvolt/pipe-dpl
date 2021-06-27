<?php
declare(strict_types = 1);

namespace app\components\db;

use app\models\sys\permissions\traits\ActiveQueryPermissionsTrait;
use pozitronik\traits\models\ActiveQuery as VendorActiveQuery;

/**
 * Trait ActiveQueryTrait
 * Каст расширения запросов
 */
class ActiveQuery extends VendorActiveQuery {
	use ActiveQueryPermissionsTrait;
}