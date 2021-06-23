<?php
declare(strict_types = 1);

namespace app\models\dealers\active_record\references;

use pozitronik\references\models\CustomisableReference;

/**
 * Class RefDealersGroups
 */
class RefDealersGroups extends CustomisableReference {

	public string $menuCaption = "Справочник групп дилеров";
	public $moduleId = "Дилеры";

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'ref_dealers_groups';
	}
}