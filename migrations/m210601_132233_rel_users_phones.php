<?php
declare(strict_types = 1);
use  pozitronik\relations\models\RelationMigration;

/**
 * Class m210601_132233_rel_users_phones
 */
class m210601_132233_rel_users_phones extends RelationMigration {
	public string $table_name = 'relation_users_to_phones';
	public string $first_key = 'user_id';
	public string $second_key = 'phone_id';
}
