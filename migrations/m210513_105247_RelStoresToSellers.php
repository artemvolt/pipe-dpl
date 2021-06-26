<?php
declare(strict_types = 1);
use pozitronik\relations\models\RelationMigration;

/**
 * Class m210513_105247_RelStoresToSellers
 */
class m210513_105247_RelStoresToSellers extends RelationMigration {
	public string $table_name = "relation_stores_to_sellers";
	public string $first_key = "store_id";
	public string $second_key = "seller_id";

}
