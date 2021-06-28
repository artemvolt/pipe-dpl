<?php
declare(strict_types = 1);
use pozitronik\relations\models\RelationMigration;

/**
 * Class m210512_104707_RelStoresToProduct
 */
class m210512_104707_RelStoresToProduct extends RelationMigration {

	public string $table_name = "relation_stores_to_product";
	public string $first_key = "store_id";
	public string $second_key = "product_id";

}
