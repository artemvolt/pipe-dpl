<?php
declare(strict_types = 1);

return [
	'class' => 'yii\db\Connection',
	'dsn' => 'mysql:host=172.16.13.237;dbname=dpldb',
	'username' => 'dpldb',
	'password' => 'dpldb',
	'charset' => 'utf8',

	// Schema cache options (for production environment)
	'enableSchemaCache' => true,
	'schemaCacheDuration' => 60,
	'schemaCache' => 'cache',
];
