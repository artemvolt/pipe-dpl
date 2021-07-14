<?php
declare(strict_types = 1);

namespace app\modules\dol\components\requestUserProfile;

use app\modules\dol\components\BaseHandler;
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
	 */
	public function handle(Response $response):array {
		if (BaseHandler::UNAUTHORIZED_CODE === $response->statusCode) {
			throw new UnauthorizedHttpException('Не смогли пройти аутентификацию в ДОЛ');
		}
		if (BaseHandler::FORBIDDEN_CODE === $response->statusCode) {
			throw new ForbiddenHttpException('Требуется наличие прав доступа в ДОЛ');
		}
		return BaseHandler::handle($response);
	}
}
