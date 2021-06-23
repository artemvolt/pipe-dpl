<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\core\prototypes\DefaultController;
use app\models\reward\Rewards;
use app\models\reward\RewardsSearch;

/**
 * Class RewardsController
 */
class RewardsController extends DefaultController {

	public string $modelClass = Rewards::class;
	public string $modelSearchClass = RewardsSearch::class;

	/**
	 * @inheritDoc
	 */
	public function getViewPath():string {
		return '@app/views/rewards';
	}
}