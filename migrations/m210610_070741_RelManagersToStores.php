<?php
declare(strict_types = 1);
use pozitronik\core\models\RelationMigration;

/**
 * Class m210610_070741_RelManagersToStores
 */
class m210610_070741_RelManagersToStores extends RelationMigration {
	public string $table_name = 'relation_managers_to_stores';
	public string $first_key = 'manager_id';
	public string $second_key = 'store_id';

}
