<?php
declare(strict_types = 1);
use pozitronik\core\models\RelationMigration;

/**
 * Class m210611_101742_RelDealersToStores
 */
class m210611_101742_RelDealersToStores extends RelationMigration {
	public string $table_name = 'relation_dealers_to_stores';
	public string $first_key = 'dealer_id';
	public string $second_key = 'store_id';

}
