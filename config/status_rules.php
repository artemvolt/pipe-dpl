<?php
declare(strict_types = 1);

/*При наличии одноимённого файла в подкаталоге /local конфигурация будет взята оттуда*/
if (file_exists($localConfig = __DIR__.DIRECTORY_SEPARATOR.'local'.DIRECTORY_SEPARATOR.basename(__FILE__))) return require $localConfig;

const CREATED = 1;//создан
const SENT = 2;//отправлен
const RECEIVED = 3;//получен
const DENIED = 4;//отказ поставки
const CANCELLED = 5;//отмена заказа отправителем
const DONE = 6;//полностью обработано
const ARCHIVE = 7;//в архиве

/*
 * todo: Мультиобработчик для применения статусов
*/
return [

];