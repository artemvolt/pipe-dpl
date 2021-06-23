<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\core\prototypes\ActiveRecordTrait;
use app\models\core\prototypes\DefaultController;
use app\models\managers\Managers;
use app\models\managers\ManagersSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class ManagersController
 */
class ManagersController extends DefaultController {

	protected const DEFAULT_TITLE = "Менеджеры";

	public string $modelClass = Managers::class;
	public string $modelSearchClass = ManagersSearch::class;
	public bool $enablePrototypeMenu = false;

	/**
	 * @inheritDoc
	 */
	public function getViewPath():string {
		return '@app/views/managers';
	}

	/**
	 * @inheritDoc
	 */
	public function actionCreate() {
		/** @var Managers $model */
		$model = $this->model;
		$model->scenario = $model::SCENARIO_CREATE;
		if (Yii::$app->request->post('ajax')) {
			return $this->asJson($model->validateModelFromPost());
		}
		$errors = [];
		$posting = $model->createModelFromPost($errors);
		if (true === $posting) {
			$model->createAccess();
			return $this->redirect('index');
		}
		/* Пришёл постинг, но есть ошибки */
		if ((false === $posting) && Yii::$app->request->isAjax) {
			return $this->asJson($errors);
		}

		/* Постинга не было */
		return (Yii::$app->request->isAjax)
			?$this->renderAjax('modal/create', ['model' => $model])
			:$this->render('create', ['model' => $model]);
	}

	/**
	 * @inheritDoc
	 */
	public function actionEdit(int $id) {
		/** @var Managers $model */
		if (null === $model = $this->model::findOne($id)) {
			throw new NotFoundHttpException();
		}

		/** @var ActiveRecordTrait $model */
		if (Yii::$app->request->post('ajax')) {/* запрос на ajax-валидацию формы */
			return $this->asJson($model->validateModelFromPost());
		}
		$errors = [];
		$posting = $model->updateModelFromPost($errors);

		if (true === $posting) {/* Модель была успешно прогружена */
			$model->modifyName();
			return $this->redirect('index');
		}
		/* Пришёл постинг, но есть ошибки */
		if ((false === $posting) && Yii::$app->request->isAjax) {
			return $this->asJson($errors);
		}
		/* Постинга не было */
		return (Yii::$app->request->isAjax)
			?$this->renderAjax('modal/edit', ['model' => $model])
			:$this->render('edit', ['model' => $model]);
	}

}