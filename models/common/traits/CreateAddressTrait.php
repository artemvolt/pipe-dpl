<?php
declare(strict_types = 1);

namespace app\models\common\traits;

use app\models\addresses\Addresses;

/**
 * Trait CreateAddressTrait
 *
 * @property string $index
 * @property string $area
 * @property string $region
 * @property string $city
 * @property string $street
 * @property string $building
 *
 * @property Addresses $addressesInstance
 */
trait CreateAddressTrait {
	private $addressInstance;

	/**
	 * Создает/обновляет адрес
	 * @param array $post
	 * @return bool
	 */
	public function createUpdateAddress(array $post):bool {
		$address = $this->relAddress??new Addresses();
		$isNewRecord = $address->isNewRecord;
		$address->load($post);
		$isSave = $address->save();
		if ($isNewRecord && $isSave) {
			$this->relAddress = $address;
			return true;
		}
		return $isSave;
	}

	/**
	 * @return Addresses
	 */
	public function getAddressesInstance():Addresses {
		if (null === $this->addressInstance) {
			$this->addressInstance = new Addresses();
		}
		return $this->addressInstance;
	}

}