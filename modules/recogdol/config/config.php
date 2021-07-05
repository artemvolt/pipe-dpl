<?php
/** @noinspection PhpIncludeInspection */
/** @noinspection UsingInclusionReturnValueInspection */
declare(strict_types = 1);

/*При наличии одноимённого файла в подкаталоге /local конфигурация будет взята оттуда*/
if (file_exists($localConfig = __DIR__.DIRECTORY_SEPARATOR.'local'.DIRECTORY_SEPARATOR.basename(__FILE__))) {
	return require $localConfig;
}

return [
	'params' => [
		'connection' => [
			'host' => '',
			'sslCertificate' => false
		]
	]
];
