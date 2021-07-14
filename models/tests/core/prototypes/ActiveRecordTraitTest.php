<?php
declare(strict_types = 1);

namespace app\models\tests\core\prototypes;

use app\components\db\ActiveRecordTrait;
use DomainException;

/**
 * Trait ActiveRecordTraitTest
 * @package app\models\tests\core\prototypes
 */
trait ActiveRecordTraitTest {
	use ActiveRecordTrait;

	public function saveAndReturn():self {
		if (!$this->save()) {
			throw new DomainException(
				"Не получилось сохранить запись".
				implode(". ", $this->getFirstErrors())
			);
		}
		return $this;
	}
}