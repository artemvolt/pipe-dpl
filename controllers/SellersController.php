<?php
declare(strict_types = 1);

namespace app\controllers;

use app\components\exceptions\ValidateException;
use app\components\web\DefaultController;
use app\models\addresses\active_record\AddressesAR;
use app\models\addresses\Addresses;
use app\models\seller\active_record\SellersAR;
use app\models\seller\SellerMiniAssignWithStoreForm;
use app\models\seller\SellerMiniService;
use app\models\seller\Sellers;
use app\models\seller\SellersSearch;
use app\modules\notifications\models\Notifications;
use DomainException;
use Throwable;
use Yii;
use ReflectionClass;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
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
		$model->scenario = SellersAR::SCENARIO_CREATE;
		if (Yii::$app->request->post('ajax')) {
			return $this->asJson($model->validateModelFromPost());
		}
		$errors = [];
		$posting = $model->createModelFromPost($errors);
		if (true === $posting) {
			$model->uploadAttributes();
			$model->createAccess();
			if (!empty(array_filter(Yii::$app->request->post((new ReflectionClass(new Addresses()))->getShortName())))) {
				$model->createUpdateAddress(Yii::$app->request->post());
			}
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

		$model->scenario = SellersAR::SCENARIO_EDIT;
		/** @var SellersAR $model */
		$address = $model->relAddress;
		if ($address) {
			$address->scenario = AddressesAR::SCENARIO_EDIT_SELLER;
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
			$model->createUpdateAddress(Yii::$app->request->post());
			return $this->redirect('index');
		}
		/* Пришёл постинг, но есть ошибки */
		if ((false === $posting) && Yii::$app->request->isAjax) {
			return $this->asJson($errors);
		}

		/* Постинга не было */
		return (Yii::$app->request->isAjax)
			?$this->renderAjax('modal/edit', compact('model', 'address'))
			:$this->render('edit', compact('model', 'address'));
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

	/**
	 * @return Response|string
	 * @throws Exception
	 * @throws ForbiddenHttpException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionAssignMiniWithStore() {
		$request = Yii::$app->request;
		$assignForm = new SellerMiniAssignWithStoreForm();
		$currentUserStores = Yii::$app->user->identity->getStoresViaRole();

		if ($assignForm->load($request->post()) && $assignForm->validate()) {
			$service = new SellerMiniService();
			try {
				$service->assignWithStore($assignForm);
				Notifications::message("Продавец успешно привязан к торговой точке");
				return $this->redirect(Url::toRoute(['sellers/index']));
			} catch (DomainException $e) {
				Notifications::message($e->getMessage());
			}
		}
		return $this->render('assign_mini_with_store', compact('assignForm', 'currentUserStores'));
	}

}
