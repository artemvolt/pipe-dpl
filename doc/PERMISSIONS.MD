# Редактор доступов

## Постулирование идеи
* Постулат первый: всё, что не разрешено, запрещено.
* Постулат второй: доступы атомарны.

В переводе на гуманитарный, это означает: при проверке доступности какого-то действия для пользователя, исходим из того, что оно ему недоступно, если только нет конкретного разрешения. При этом такое разрешение даёт доступ **только** к этому действию, и никакому другому.

## Как это устроено?

Правила доступов (далее — просто *доступы*) описываются, как::
- **name**: мнемоническое уникальное название доступа;
- **comment**: описание разрешения, которое доступ представляет (необязательно);
- **controller**,
- **action**,
- **verb**: используются для разрешений доступа к контроллерам, действиям контроллеров и методам действий.

Правила привязываются к объекту пользователя `Users` через трейт `UsersPermissionsTrait`. Трейт добавляет атрибуты `$relatedPermissions` (связывающий `Users` и `Permissions`) и `$relatedPermissionsCollections` (связывающий `Users` и `PermissionsCollections`), через которые можно манипулировать разрешениями на уровне кода.

Проверить, есть ли у пользователя разрешение, можно методом `hasPermission()`, добавляемым тем же трейтом. Например:
```php
/** @var app\models\sys\users\Users $skywalker */
$skywalker->hasPermission(['use_force', 'fight_with_lightsaber'], app\models\sys\permissions\Permissions::LOGIC_AND);
```
где `use_force` и `fight_with_lightsaber` — названия доступов, а `Permissions::LOGIC_AND` — одна из констант логики проверки доступов (подробности смотрите в PHPDoc трейта).

Если доступы с такими названиями созданы, привязаны к пользователю и попадают под заданную логику проверки, метод вернёт `true`. Во всех иных случаях вернётся `false`.

## Доступы к контроллерам и действиям. PermissionFilter.

Если в доступе задан параметр `controller`, то он может работать, как предоставление доступа к указанному контроллеру. Если вместе с `controller` задан параметр `action`, то доступ предоставляется **только** к указанному действию этого контроллера. А если вместе с `action` указан параметр `verb`, то доступ предоставляется только к указанному методу для этого действия этого контроллера.

Чтобы эти проверки работали, в контроллере нужно подключить фильтр `PermissionFilter`:

```php
public function behaviors():array {
	return [
		'access' => [
			'class' => app\models\sys\permissions\filters\PermissionFilter::class
		],
		...
	];
}
```

## Группы доступов

Для того чтобы не привязывать к каждому пользователю каждый отдельный доступ, можно объединить несколько доступов в *группу* (в комментариях встречаются названия *коллекция* и *набор*). Например, все доступы, положенные администратору, объединить в группу **admin**, и уже эту группу привязывать к пользователям. Редактирование доступов внутри такой группы автоматически изменит доступы и у каждого пользователя, связанного с этой группой.

Доступы всех групп пользователя суммируются меж собой и с доступами, назначенными пользователю напрямую. Т.е., если у нас есть группы:

	'Доступы на чтение' => ['доступ №1', 'доступ №7', 'доступ №14']
	'Доступы на запись' => ['доступ №2', 'доступ №9', 'доступ №42']
а у пользователя $user привязаны обе эти группы и доступ 'доступ №13', то полный его набор доступов будет

	['доступ №1', 'доступ №7', 'доступ №14', 'доступ №2', 'доступ №9', 'доступ №42', 'доступ №13']