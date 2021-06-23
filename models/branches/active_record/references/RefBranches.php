<?php
declare(strict_types = 1);

namespace app\models\branches\active_record\references;

use pozitronik\references\models\CustomisableReference;

/**
 * Class RefBranches
 */
class RefBranches extends CustomisableReference {

	public string $menuCaption = "Справочник филиалов";
	public $moduleId = "Филиалы";

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'ref_branches';
	}
}