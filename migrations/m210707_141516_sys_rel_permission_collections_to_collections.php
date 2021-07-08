<?php
declare(strict_types = 1);
use pozitronik\relations\models\RelationMigration;

/**
 * Class m210707_141516_sys_rel_permission_collections_to_collections
 */
class m210707_141516_sys_rel_permission_collections_to_collections extends RelationMigration {
	public string $table_name = 'sys_relation_permissions_collections_to_permissions_collections';
	public string $first_key = 'master_id';
	public string $second_key = 'slave_id';

}
