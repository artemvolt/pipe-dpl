<?php
declare(strict_types = 1);

namespace app\models\regions\active_record\references;

use pozitronik\references\models\CustomisableReference;

/**
 * Class RefRegions
 */
class RefRegions extends CustomisableReference {

	public string $menuCaption = "Справочник регионов";
	public $moduleId = "Регионы";

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'ref_regions';
	}
}