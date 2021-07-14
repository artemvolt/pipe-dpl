<?php
declare(strict_types = 1);

namespace app\modules\api\controllers;

use app\components\exceptions\ValidateException;
use app\models\seller\ConfirmSmsAfterRegisterForm;
use app\models\seller\RegisterMiniSellerForm;
use app\models\seller\SellerMiniService;
use app\models\seller\Sellers;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use DateTime;
use Throwable;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Class SellerController
 * @package app\modules\api\controllers
 */
class SellerController extends Controller {
	/**
	 * {@inheritdoc}
	 */
	public function behaviors():array {
		return [
			'contentNegotiator' => [
				'class' => ContentNegotiator::class,
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
			],
			'verbFilter' => [
				'class' => VerbFilter::class,
				'actions' => $this->verbs(),
			]
		];
	}

	/**
	 * @return array
	 * @throws ValidateServerErrors
	 * @throws Throwable
	 */
	public function actionRegister():array {
		try {
			$request = Yii::$app->request;
			$form = new RegisterMiniSellerForm();
			$form->phone_number = $request->post('phone_number');
			$form->accept_agreement = (bool)$request->post('accept_agreement', false);
			/**
			 * @var Sellers $registeredSeller
			 */
			$registeredSeller = Yii::$app->db->transaction(function() use ($form) {
				$service = new SellerMiniService();
				return $service->register($form);
			});

			$time = new DateTime();
			$time->modify('+30 seconds');
			return [
				'data' => [
					'result' => true,
					'expiredAt' => $time->format('Y-m-d\TH:i:sO'),
					'verificationToken' => $registeredSeller->getVerificationToken()
				]
			];
		} catch (ValidateServerErrors $e) {
			throw new ValidateServerErrors($e->mapErrors([
				'phoneAsLogin' => 'phone_number'
			]));
		}
	}

	/**
	 * @return array
	 * @throws ValidateServerErrors
	 * @throws ValidateException
	 */
	public function actionConfirmSms():array {
		try {
			$request = Yii::$app->request;
			$form = new ConfirmSmsAfterRegisterForm();
			$form->phoneNumber = $request->post('phone_number');
			$form->smsCode = $request->post('sms_code');
			$form->verificationToken = $request->post('verification_token');

			$service = new SellerMiniService();
			$service->confirmSmsAfterRegister($form);

			return ['data' => ['result' => true]];
		} catch (ValidateServerErrors $e) {
			throw new ValidateServerErrors($e->mapErrors([
				'phoneAsLogin' => 'phoneNumber',
				'code' => 'smsCode',
				'verificationToken' => 'verificationToken'
			]));
		}
	}
}