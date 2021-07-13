<?php
declare(strict_types = 1);

namespace app\models\tests\sys\permissions;

use app\models\sys\permissions\PermissionsCollections;
use app\models\tests\core\prototypes\ActiveRecordTraitTest;

/**
 * Class PermissionsCollectionsTests
 * @package app\models\tests\sys\permissions
 */
class PermissionsCollectionsTests extends PermissionsCollections {
	use ActiveRecordTraitTest;

	public static function create(string $name):self {
		$self = new self();
		$self->name = $name;
		return $self;
	}
}