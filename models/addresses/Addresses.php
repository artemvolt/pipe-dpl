<?php
declare(strict_types = 1);

namespace app\models\addresses;

use app\models\addresses\active_record\AddressesAR;

/**
 * Class Addresses
 * @package app\models\addresses
 *
 * @property string $addressString Полный адрес
 */
class Addresses extends AddressesAR {
	/**
	 * @return string
	 */
	public function getAddressString():string {
		return trim(
			implode(
				', ',
				array_filter([$this->index, $this->refRegion->name, $this->region, $this->city, $this->street, $this->building])
			)
		);
	}
}