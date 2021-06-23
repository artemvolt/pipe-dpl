<?php
declare(strict_types = 1);

namespace app\models\store\active_record\references;

use pozitronik\references\models\CustomisableReference;

/**
 * Справочник каналов продаж
 */
class RefSellingChannels extends CustomisableReference {

	public string $menuCaption = "Каналы продаж";
	public $moduleId = "Магазины";

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'ref_selling_channels';
	}
}