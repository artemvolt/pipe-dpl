<?php
declare(strict_types = 1);

namespace app\models\tests\dealer;

use app\models\dealers\Dealers;
use app\models\tests\core\prototypes\ActiveRecordTraitTest;
use Exception;

/**
 * Class DealersTests
 * @package app\models\tests\dealer
 */
class DealersTests extends Dealers {
	use ActiveRecordTraitTest;

	/**
	 * @param string $name
	 * @return DealersTests
	 * @throws Exception
	 */
	public static function create(string $name):DealersTests {
		$self = new self();
		$self->name = $name;
		$self->code = (string)random_int(1, 1000);
		$self->client_code = (string)random_int(1, 1000);
		$self->type = 1;
		$self->branch = 1;
		$self->group = 1;
		return $self;
	}
}