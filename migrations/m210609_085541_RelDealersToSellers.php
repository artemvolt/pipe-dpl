<?php
declare(strict_types = 1);
use pozitronik\core\models\RelationMigration;

/**
* Class m210609_085541_RelDealersToSellers
*/
class m210609_085541_RelDealersToSellers extends RelationMigration {
	public string $table_name = "relation_dealers_to_sellers";
	public string $first_key = "dealer_id";
	public string $second_key = "seller_id";
}
