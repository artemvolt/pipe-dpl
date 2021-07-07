<?php
declare(strict_types = 1);

namespace app\controllers;

use app\components\exceptions\ValidateException;
use app\components\web\DefaultController;
use app\models\seller\invite_link\CreateSellerInviteLinkForm;
use app\models\seller\invite_link\EditSellerInviteLink;
use app\models\seller\SellerInviteLink;
use app\models\seller\SellerInviteLinkSearch;
use app\models\seller\SellerMiniService;
use app\modules\notifications\models\Notifications;
use DomainException;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\di\NotInstantiableException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ManagersController
 */
class SellersInviteLinksController extends DefaultController {

	protected const DEFAULT_TITLE = "Приглашения для продавцов";

	public string $modelClass = SellerInviteLink::class;
	public string $modelSearchClass = SellerInviteLinkSearch::class;

	/**
	 * @inheritDoc
	 */
	public function getViewPath():string {
		return '@app/views/sellers-invite-links';
	}

	/**
	 * @return string|Response
	 * @throws Throwable
	 * @throws InvalidConfigException
	 * @throws Exception
	 * @throws NotInstantiableException
	 * @throws ForbiddenHttpException
	 */
	public function actionCreate() {
		$createForm = new CreateSellerInviteLinkForm();

		$request = Yii::$app->request;
		if ($createForm->load($request->post()) && $createForm->validate()) {
			try {
				$service = new SellerMiniService();
				$savedLink = $service->createInviteLink($createForm);
				if ($savedLink->email) {
					Notifications::message("Ссылка успешно отправлена на почту.");
				}
				if ($savedLink->phone_number) {
					Notifications::message("Ссылка успешно отправлена на номер моб.телефона.");
				}
				return $this->redirect('index');
			} catch (DomainException $exception) {
				Notifications::message($exception->getMessage());
			}
		}
		/* Постинга не было */
		return $this->render('create', ['createForm' => $createForm]);
	}

	/**
	 * @param int $id
	 * @return string|Response
	 * @throws NotFoundHttpException
	 * @throws ValidateException
	 * @throws Exception
	 * @throws ForbiddenHttpException
	 */
	public function actionEdit(int $id) {
		/**
		 * @var SellerInviteLink $existentModel
		 */
		if (null === $existentModel = $this->model::findOne($id)) {
			throw new NotFoundHttpException();
		}

		$request = Yii::$app->request;
		$editForm = new EditSellerInviteLink([
			'existentIdLink' => $existentModel->id,
			'phone_number' => $existentModel->phone_number,
			'email' => $existentModel->email
		]);
		if ($editForm->load($request->post()) && $editForm->validate()) {
			try {
				$editForm->edit();
				if ($editForm->repeatEmailNotify) {
					Notifications::message("Письмо успешно отправлено");
				}
				if ($editForm->repeatPhoneNotify) {
					Notifications::message("SMS успешно отправлено");
				}
				Notifications::message("Запись успешно обновлена");
				return $this->refresh();
			} catch (DomainException $e) {
				Notifications::message($e->getMessage());
			}
		}

		/* Постинга не было */
		return $this->render('edit', compact('editForm', 'existentModel'));
	}

}
