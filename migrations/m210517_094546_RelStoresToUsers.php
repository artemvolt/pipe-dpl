<?php
declare(strict_types = 1);
use pozitronik\core\models\RelationMigration;

/**
* Class m210517_094546_RelStoresToUsers
*/
class m210517_094546_RelStoresToUsers extends RelationMigration {
	public string $table_name = "relation_stores_to_users";
	public string $first_key = "store_id";
	public string $second_key = "user_id";

}
