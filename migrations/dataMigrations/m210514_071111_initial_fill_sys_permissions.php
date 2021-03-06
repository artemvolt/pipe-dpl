<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210514_071111_initial_fill_sys_permissions
 */
class m210514_071111_initial_fill_sys_permissions extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->execute("INSERT INTO sys_permissions (name, controller, action, verb, comment, priority) VALUES 
		('Личный кабинет', '', null, null, 'Доступ к Личному кабинету', 0),
		('Верификация через Госуслуги', '', null, null, 'Верификация данных через Госуслуги', 0),
		('Планы на ТТ', '', null, null, 'Инфо о планах на Торговой точке (ТТ)', 0),
		('Планы на сотрудника', '', null, null, 'ИНфо о планах на сотрудника', 0),
		('Выполнение планов на ТТ', '', null, null, 'Инфо о выполнении планов на Торговой точке (ТТ)', 0),
		('Выполнение планов сотрудника', '', null, null, 'Инфо о выполнении планов на сотрудника', 0),
		('Просмотр ПД', '', null, null, 'Просмотр собственных персональных данных', 0),
		('Редактирование ПД', '', null, null, 'Редактирование собственных персональных данных', 0),
		('Просмотр РД', '', null, null, 'Просмотр рабочих данных(РД). Должна отображаться информация о перечне оказанных услуг, бонусных начислениях, тарифах начисления бонусов, достижений, целей мотивационной программы, информация об использованных бонусах, статусы подтверждения услуг и причины неподтверждения', 0),
		('Управление бонусами', '', null, null, 'Управление бонусами(оплата услуг ВК, перечисление ДС на кобрендинговую карту)', 0),
		('Связь по ussd', '', null, null, 'Альтернативный способ связи с Backoffice СНМП с помощью ussd запросов (информация по бонусам, управление бонусами)', 0),
		('Создание запросов', '', null, null, 'Заведения инцидентов в случае технических ошибок и прочих вопросов', 0),
		('Просмотр списка сотрудников', '', null, null, 'Просмотр списка сотрудников партнера', 0),
		('Просмотр списка ТТ', '', null, null, 'Просмотр списка торговых точек (ТТ) партнера', 0),
		('Планы на регион', '', null, null, 'Инфо о планах на регион', 0),
		('Выполнение планов на регион', '', null, null, 'Инфо о выполнении планов на регион', 0),
		('Регистрация продавцов', '', null, null, 'Возможность регистрации продавцов', 0),
		('Просмотр списка партнеров для территориального специалиста', '', null, null, 'Возможность отображения партнеров с привязкой к региону, ТТ, сотрудников', 0),
		('Планы на партнера', '', null, null, 'Инфо о планах на партнера', 0),
		('Выполнение планов на партнера', '', null, null, 'Инфо о выполнении планов на партнера', 0),
		('Заведение заявлений на присоединение', '', null, null, 'Возможность заведения электронных заявлений на присоединение к СНМП', 0),
		('Обработка заявлений на присоединение', '', null, null, 'Возможность обработки электронных заявлений на присоединение к СНМП', 0),
		('Подтверждения/регистрации сотрудников бэк-офисе', '', null, null, 'Возможность подтверждения/регистрации сотрудника в бэк-офисе СНМП', 0),
		('Управление продавцами', '', null, null, '', 0),
		('Управление партнерами', '', null, null, '', 0),
		('Управление территориальными специалистами', '', null, null, '', 0),
		('Управление менеджерами ШК', '', null, null, '', 0),
		('Управление сотрудниками ШК', '', null, null, '', 0),
		('Управление призами', '', null, null, 'Возможность управления призами (начисление, создание, редактирование) с зависимостью от партнера, региона, тт, услуги, в том числе возможность корректировок при выявлении фрода', 0),
		('Отчеты о бонусах/призах', '', null, null, 'Отчеты о бонусных начислениях/расходах, заказанных призах', 0),
		('Блок ГПОД', '', null, null, 'Блок оператора горячей линии (ГПОД)', 0),
		('Техническая поддержка', '', null, null, 'Интерфейс технической поддержки', 0),
		('Работа с инцидентами', '', null, null, 'Входит в блок оператора горячей линии', 0),
		('История достижений и начислений', '', null, null, 'Входит в блок оператора горячей линии', 0),
		('Действующие достижения продавца', '', null, null, 'Входит в блок оператора горячей линии', 0),
		('История зарегистрированных услуг', '', null, null, 'Входит в блок оператора горячей линии', 0),
		('История использования бонусов', '', null, null, 'Входит в блок оператора горячей линии', 0),
		('Баланс бонусов продавца', '', null, null, 'Входит в блок оператора горячей линии', 0),
		('Карточка участника программы', '', null, null, 'Входит в блок оператора горячей линии', 0),
		('История обращений продавца', '', null, null, 'Входит в блок оператора горячей линии', 0),
		('Список вопросов тем горячей линии', '', null, null, 'Входит в блок оператора горячей линии', 0),
		('История смс запросов', '', null, null, 'Входит в блок оператора горячей линии', 0),
		('Перенаправление обращений', '', null, null, 'Входит в блок оператора горячей линии. Возможность перенаправления обращений тех. поддержке партнера', 0),
		('Просмотр списка партнеров для регионального менеджера', '', null, null, 'Возможность отображения партнеров с привязкой к региону, ТТ, сотрудникам/каналу продаж/дилеру.', 0),
		('Загрузка планов мотивационной программы ', '', null, null, 'Возможность загрузки планов мотивационной программы на сотрудника/ТТ/партнера/регион', 0),
		('Настройка тарифов начисления бонусов уровневой мотивации и фиксированного процента', '', null, null, 'Возможность настройки тарифов начисления бонусов уровневой мотивации и фиксированного процента в зависимости сотрудника/ТТ/партнера/региона', 0);
    ");
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		return false;//это миграция данных, она не откатывается
	}

}
