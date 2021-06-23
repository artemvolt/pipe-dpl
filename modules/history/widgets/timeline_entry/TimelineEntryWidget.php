<?php
declare(strict_types = 1);

namespace app\modules\history\widgets\timeline_entry;

use app\modules\history\models\TimelineEntry;
use yii\base\Widget;

/**
 * Виджет элемента таймлайна
 * Class TimelineWidget
 *
 * @property TimelineEntry $entry
 */
class TimelineEntryWidget extends Widget {
	public $entry;

	/**
	 * Функция возврата результата рендеринга виджета
	 * @return string
	 */
	public function run():string {
		return $this->render('timeline_entry', [
			'entry' => $this->entry
		]);
	}
}
