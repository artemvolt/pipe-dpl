<?php
declare(strict_types = 1);

use app\controllers\ManagersController;
use app\controllers\PermissionsCollectionsController;
use app\controllers\PermissionsController;
use app\controllers\SellersController;
use app\controllers\SellersInviteLinksController;
use app\controllers\SiteController;
use app\controllers\StoresController;
use app\controllers\UsersController;
use app\components\web\DefaultController;
use app\models\sys\users\Users;
use app\modules\fraud\controllers\IndexController as FraudIndexController;
use app\modules\fraud\FraudModule;
use app\modules\history\HistoryModule;
use app\widgets\smartadmin\sidebar\SideBarWidget;
use pozitronik\filestorage\FSModule;
use pozitronik\references\ReferencesModule;
use pozitronik\sys_exceptions\SysExceptionsModule;
use yii\helpers\Url;
use app\controllers\DbController;
use app\controllers\DealersController;

?>

<?= SideBarWidget::widget([
	'items' => [
		[
			'label' => 'Домой',
			'url' => [Url::home()],
			'iconClass' => 'fa-home'
		],
		[
			'label' => UsersController::Title(),
			'iconClass' => 'fa-users-cog',
			'url' => [UsersController::to('index')],
			'visible' => UsersController::hasPermission('index')
			//'visible' => UsersController::hasPermission() для проверки доступа ко всему контроллеру и отключения всего списка
		],
		[
			'label' => DealersController::Title(),
			'url' => [DealersController::to('index')],
			'iconClass' => 'fa-building',
			'visible' => DealersController::hasPermission('index')
		],
		[
			'label' => ManagersController::Title(),
			'url' => [ManagersController::to('index')],
			'iconClass' => 'fa fa-user-tie',
			'visible' => ManagersController::hasPermission('index')
		],
		[
			'label' => StoresController::Title(),
			'url' => [StoresController::to('index')],
			'iconClass' => 'fa-store',
			'visible' => StoresController::hasPermission('index')
		],
		[
			'label' => SellersController::Title(),
			'url' => '#',
			'iconClass' => 'fa-smile-beam',
			'visible' => SellersController::hasPermission('index'),
			'items' => [
				[
					'label' => "Управление",
					'url' => [SellersController::to('index')],
					'visible' => SellersController::hasPermission('index')
				],
				[
					'label' => 'Приглашения',
					'url' => [SellersInviteLinksController::to("index")],
					'visible' => SellersInviteLinksController::hasPermission('index')
				],
			],

		],
		[
			'label' => 'Фродмониторинг',
			'url' => [FraudModule::to('index/list')],
			'iconClass' => 'fa-alien',
			'visible' => FraudIndexController::hasPermission('list')
		],
		[
			'label' => 'Прототипирование',
			'url' => '#',
			'iconClass' => 'fa-digging',
			'items' => DefaultController::MenuItems()
		],
		[
			'label' => 'Доступы',
			'url' => '#',
			'iconClass' => 'fa-lock',
			'items' => [
				[
					'label' => 'Редактор разрешений',
					'url' => [PermissionsController::to('index')],
					'visible' => PermissionsController::hasPermission('index')
				],
				[
					'label' => 'Группы разрешений',
					'url' => [PermissionsCollectionsController::to('index')],
					'visible' => PermissionsCollectionsController::hasPermission('index')
				],
			],

		],
		[
			'label' => 'Система',
			'url' => '#',
			'iconClass' => 'fa-wrench',
			'items' => [
				[
					'label' => 'Справочники',
					'url' => [ReferencesModule::to('references')],
				],
				[
					'label' => 'Протокол сбоев',
					'url' => [SysExceptionsModule::to('index')]
				],
				[
					'label' => 'Процессы на БД',
					'url' => [DbController::to('process-list')]
				],
				[
					'label' => 'Файловый менеджер',
					'url' => [FSModule::to('index')]
				],
				[
					'label' => 'История изменений',
					'url' => [HistoryModule::to('index')]
				],
				[
					'label' => 'Настройки системы',
					'url' => [SiteController::to('options')]
				]
			],
			'visible' => Users::Current()->hasPermission(['system'])
		],
		[
			'label' => 'REST API',
			'url' => '#',
			'iconClass' => 'fa-cloud',
			'items' => [
				[
					'label' => 'Пользователи',
					'url' => ['/api/users'],
				]
			],
			'visible' => Users::Current()->hasPermission(['system'])
		],

	]
]) ?>