<?php
declare(strict_types = 1);

namespace app\models\tests\store;

use app\models\dealers\active_record\relations\RelDealersToStores;
use app\models\store\active_record\relations\RelStoresToUsers;
use app\models\store\Stores;
use app\models\tests\core\prototypes\ActiveRecordTraitTest;
use DomainException;

/**
 * Class StoresTests
 * @package app\models\tests\store
 */
class StoresTests extends Stores {
	use ActiveRecordTraitTest;

	/**
	 * @param string $name
	 * @return StoresTests
	 */
	public static function create(string $name):StoresTests {
		$self = new self();
		$self->name = $name;
		$self->type = 1;
		$self->selling_channel = 1;
		$self->branch = 1;
		$self->region = 1;
		return $self;
	}

	public function assignWithDealer(int $dealerId):self {
		$assign = new RelDealersToStores();
		$assign->dealer_id = $dealerId;
		$assign->store_id = $this->id;
		if (!$assign->save()) {
			throw new DomainException("НЕ удалось привязать");
		}
		return $this;
	}

	/**
	 * @param int $id
	 */
	public function assignWithUser(int $id):void {
		$assign = new RelStoresToUsers();
		$assign->store_id = $this->id;
		$assign->user_id = $id;
	}
}