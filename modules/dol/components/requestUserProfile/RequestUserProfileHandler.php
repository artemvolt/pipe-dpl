<?php
declare(strict_types = 1);

namespace app\modules\dol\components\requestUserProfile;

use app\modules\dol\components\BaseHandler;
use app\modules\dol\components\exceptions\ValidateServerErrors;
use yii\httpclient\Response;
use yii\web\ForbiddenHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Class RequestUserProfileHandler
 * @package app\modules\dol\components\RequestUserProfile
 */
class RequestUserProfileHandler {

	/**
	 * @param Response $response
	 * @return array
	 * @throws ForbiddenHttpException
	 * @throws UnauthorizedHttpException
	 * @throws ValidateServerErrors
	 */
	public function handle(Response $response):array {
		if ('401' === $response->statusCode) {
			throw new UnauthorizedHttpException('Не смогли пройти аутентификацию в ДОЛ');
		}
		if ('403' === $response->statusCode) {
			throw new ForbiddenHttpException('Требуется наличие прав доступа в ДОЛ');
		}
		return BaseHandler::handle($response);
	}
}
