<?php
declare(strict_types = 1);

namespace app\controllers;

use app\components\web\DefaultController;
use app\models\sys\permissions\PermissionsCollections;
use app\models\sys\permissions\PermissionsCollectionsSearch;

/**
 * Class PermissionsCollectionsController
 */
class PermissionsCollectionsController extends DefaultController {

	public string $modelClass = PermissionsCollections::class;
	public string $modelSearchClass = PermissionsCollectionsSearch::class;
	public bool $enablePrototypeMenu = false;

	/**
	 * @inheritDoc
	 */
	public function getViewPath():string {
		return '@app/views/permissions-collections';
	}

}