<?php
declare(strict_types = 1);

namespace app\models\managers;

use app\controllers\ManagersController;
use app\models\managers\active_record\ManagersAR;
use app\models\common\traits\CreateAccessTrait;

/**
 * Class Managers
 * Конкретный менеджер
 *
 * @property string $urlToEntity
 */
class Managers extends ManagersAR {
	use CreateAccessTrait;

	/**
	 * URL для нахождения менеджера по ID
	 * @return string
	 */
	public function getUrlToEntity():string {
		return ManagersController::to('index', ['ManagersSearch[id]' => $this->id], true);
	}
}