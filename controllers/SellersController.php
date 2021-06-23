<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\core\prototypes\DefaultController;
use app\models\seller\Sellers;
use app\models\seller\SellersSearch;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class SellersController
 */
class SellersController extends DefaultController {

	protected const DEFAULT_TITLE = "Продавцы";

	public string $modelClass = Sellers::class;
	public string $modelSearchClass = SellersSearch::class;
	public bool $enablePrototypeMenu = false;

	/**
	 * @inheritDoc
	 */
	public function getViewPath():string {
		return '@app/views/sellers';
	}

	/**
	 * @inheritDoc
	 */
	public function actionCreate() {
		/** @var Sellers $model */
		$model = $this->model;
		$model->scenario = $model::SCENARIO_CREATE;
		if (Yii::$app->request->post('ajax')) {
			return $this->asJson($model->validateModelFromPost());
		}
		$errors = [];
		$posting = $model->createModelFromPost($errors);
		if (true === $posting) {
			$model->uploadAttributes();
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
		if (null === $model = $this->model::findOne($id)) {
			throw new NotFoundHttpException();
		}

		if (Yii::$app->request->post('ajax')) {/* запрос на ajax-валидацию формы */
			return $this->asJson($model->validateModelFromPost());
		}

		$errors = [];
		/** @var Sellers $model */
		$posting = $model->updateModelFromPost($errors);

		if (true === $posting) {/* Модель была успешно прогружена */
			$model->uploadAttributes();
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

	/**
	 * @param int $id
	 * @return string|Response
	 * @throws Throwable
	 * @throws Exception
	 */
	public function actionEditUser(int $id) {
		if (null === $model = $this->model::findOne($id)) {
			throw new NotFoundHttpException();
		}

		if (Yii::$app->request->post('ajax')) {/* запрос на ajax-валидацию формы */
			return $this->asJson($model->validateModelFromPost());
		}
		$errors = [];
		/** @var Sellers $model */
		$posting = $model->updateModelFromPost($errors);

		if (true === $posting) {/* Модель была успешно прогружена */
			return $this->redirect('index');
		}
		/* Пришёл постинг, но есть ошибки */
		if ((false === $posting) && Yii::$app->request->isAjax) {
			return $this->asJson($errors);
		}

		/* Постинга не было */
		return (Yii::$app->request->isAjax)
			?$this->renderAjax('modal/edit-user', ['model' => $model])
			:$this->render('edit-user', ['model' => $model]);
	}

}