<?php
declare(strict_types = 1);

/*При наличии одноимённого файла в подкаталоге /local конфигурация будет взята оттуда*/
if (file_exists($localConfig = __DIR__.DIRECTORY_SEPARATOR.'local'.DIRECTORY_SEPARATOR.basename(__FILE__))) return require $localConfig;

use app\models\reward\Rewards;
use app\models\seller\Sellers;

return [
	Rewards::class => Rewards::class.'::status_config',
	Sellers::class => Sellers::class.'::getStatusConfig'
];