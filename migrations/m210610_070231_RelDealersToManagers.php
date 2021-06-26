<?php
declare(strict_types = 1);
use pozitronik\relations\models\RelationMigration;

/**
 * Class m210610_070231_RelDealersToManagers
 */
class m210610_070231_RelDealersToManagers extends RelationMigration {
	public string $table_name = 'relation_dealers_to_managers';
	public string $first_key = 'dealer_id';
	public string $second_key = 'manager_id';
}
