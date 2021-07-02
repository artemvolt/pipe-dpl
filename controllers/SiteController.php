<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\core\Options;
use app\models\site\LoginForm;
use app\models\site\LoginSMSForm;
use app\models\site\RegistrationForm;
use app\models\site\RestorePasswordForm;
use app\models\site\UpdatePasswordForm;
use app\models\sys\users\Users;
use pozitronik\traits\traits\ControllerTrait;
use pozitronik\helpers\ArrayHelper;
use pozitronik\sys_options\models\SysOptions;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii2mod\swagger\SwaggerUIRenderer;
use yii2mod\swagger\OpenAPIRenderer;

/**
 * @SWG\Swagger(
 *     basePath="/",
 *     produces={"application/json"},
 *     consumes={"application/x-www-form-urlencoded"},
 *     @SWG\Info(version="1.0", title="Simple API"),
 * )
 */
class SiteController extends Controller {
	use ControllerTrait;

	public $layout = 'login';

	/**
	 * @inheritdoc
	 */
	public function actions():array {
		return [
			'error' => [
				'class' => ErrorAction::class
			],
			'docs' => [
				'class' => SwaggerUIRenderer::class,
				'restUrl' => self::to('json-schema', [], true),
			],
			'json-schema' => [
				'class' => OpenAPIRenderer::class,
				'cache' => null,
				// Тhe list of directories that contains the swagger annotations.
				'scanDir' => [
					Yii::getAlias('@app/controllers/api'),
					Yii::getAlias('@app/definitions'),
				],
			],
		];
	}

	/**
	 * @param null|string $from Опциональная метка перехода. Заменить на флеш-оповещения, при необходимости.
	 * @return string|Response
	 * @throws Throwable
	 */
	public function actionLogin(?string $from = null) {
		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->doLogin()) {
			if ($model->user->is_pwd_outdated) {
				return $this->redirect('update-password');
			}
			return $this->redirect(Yii::$app->homeUrl);
		}
		return $this->render('login', [
			'login' => $model,
			'from' => $from /*unused, на будущее*/
		]);
	}

	/**
	 * @return string|Response
	 * @throws InvalidConfigException
	 */
	public function actionLoginSms() {
		$model = new LoginSMSForm();
		if ($model->load(Yii::$app->request->post())) {
			if (null === $model->smsCode) {/*не было постинга кода*/
				if ($model->doSmsLogon()) {/*но был постинг корректного логина, разрешаем ввести код*/
					return $this->render('login-sms', [
						'login' => $model,
						'firstStep' => false
					]);
				}
			} else {/*был постинг кода*/
				if ($model->doConfirmSmsLogon()) {/*Пытаемся авторизовать*/
					return $this->redirect(Yii::$app->homeUrl);
				}
				/*код неверный, повторить*/
				return $this->render('login-sms', [
					'login' => $model,
					'firstStep' => false
				]);
			}
		}
		return $this->render('login-sms', [
			'login' => $model,
			'firstStep' => true
		]);
	}

	/**
	 * logout
	 */
	public
	function actionLogout():void {
		Yii::$app->user->logout();
		$this->redirect('index');
	}

	/**
	 * На момент входа в экшен пользователь должен быть авторизован.
	 * Если при этом у него протухший пароль (перекинуло из actionLogin()), то вызывается форма смены пароля,
	 *
	 * Пользователь остаётся авторизован (это нужно для проброса данных смены пароля), но его не пускает
	 * в основном шаблоне приложения
	 *
	 * @return string|Response
	 * @throws UnauthorizedHttpException
	 */
	public
	function actionUpdatePassword() {
		/** @var Users|null $loggedUser */
		if (null === $loggedUser = Yii::$app->user->identity) {
			throw new UnauthorizedHttpException('Пользователь не авторизован');
		}

		$updatePasswordModel = new UpdatePasswordForm(['user' => $loggedUser]);
		if ($updatePasswordModel->load(Yii::$app->request->post()) && $updatePasswordModel->doUpdate()) {
			return $this->redirect(Yii::$app->homeUrl);
		}
		return $this->render('update-password', [
			'model' => $updatePasswordModel
		]);
	}

	/**
	 * Запрос на восстановление пароля, доступно только неавторизованному пользователю или пользюку,
	 * у которого протух пароль. Протухшим разрешаем потому, что они тоже могут забыть пароль.
	 */
	public
	function actionRestorePassword():string {
		if (!(Yii::$app->user->isGuest || true === ArrayHelper::getValue(Yii::$app->user->identity, 'is_pwd_outdated'))) {
			throw new ForbiddenHttpException('Восстановление пароля недоступно после авторизации');
		}
		$restorePasswordForm = new RestorePasswordForm();
		if ($restorePasswordForm->load(Yii::$app->request->post())) {/*это постинг формы с емейлом*/
			$restorePasswordForm->doSendCode();//не возвращает результат
			return $this->render('restore-password-sent');
		}

		return $this->render('restore-password', [
			'model' => $restorePasswordForm
		]);

	}

	/**
	 * @param string|null $code
	 * @return string|Response
	 */
	public
	function actionResetPassword(?string $code = null) {
		if (null === $code) return $this->redirect('restore-password');

		/*Проверка наличия пользователя с указанным кодом восстановления*/
		if (null === $restoredUser = Users::findByRestoreCode($code)) return $this->redirect('restore-password');

		$resetPasswordForm = new UpdatePasswordForm([/*UpdatePasswordForm отвечает и за сброс*/
			'user' => $restoredUser,
		]);
		if ($resetPasswordForm->load(Yii::$app->request->post()) && $resetPasswordForm->doUpdate(false)) {/*Данные пришли в посте и сброшены успешно*/
			return $this->redirect(['site/login', 'from' => 'reset']);
		}

		return $this->render('reset-password', [
			'model' => $resetPasswordForm,
			'code' => $code
		]);
	}

	/**
	 * @return Response
	 * @throws Throwable
	 */
	public
	function actionIndex():Response {
		return (Yii::$app->user->isGuest || ArrayHelper::getValue(Yii::$app->user->identity, 'is_pwd_outdated', false))?$this->redirect(ArrayHelper::getValue(Yii::$app->params, 'user.loginpage', ['site/login'])):$this->redirect(Yii::$app->homeUrl);
	}

	/**
	 * @return string
	 */
	public
	function actionError():string {
		$exception = Yii::$app->errorHandler->exception;

		if (null !== $exception) {
			return $this->render('error', [
				'exception' => $exception
			]);
		}
		return "Status: {$exception->statusCode}";
	}

	/**
	 * @return string|Response
	 */
	public
	function actionRegister() {
		$registrationForm = new RegistrationForm();
		if ($registrationForm->load(Yii::$app->request->post()) && $registrationForm->doRegister()) {
			return $this->redirect(['site/login', 'from' => 'register']);
		}
		return $this->render('register', [
			'model' => $registrationForm
		]);
	}

	/**
	 * @return string
	 * @throws Throwable
	 */
	public
	function actionOptions():string {
		$this->layout = 'main';
		return $this->render('options', [
			'boolOptions' => [
				'ASSETS_PUBLISHOPTIONS_FORCECOPY' => SysOptions::getStatic('ASSETS_PUBLISHOPTIONS_FORCECOPY', Options::ASSETS_PUBLISHOPTIONS_FORCECOPY),
				'GRANT_TYPE_ISSUE_IGNORE_TOKEN_VALIDATION' => SysOptions::getStatic('GRANT_TYPE_ISSUE_IGNORE_TOKEN_VALIDATION', Options::GRANT_TYPE_ISSUE_IGNORE_TOKEN_VALIDATION),
			],
			'optionsLabels' => Options::OPTIONS_LABELS
		]);
	}

}
