<?php
declare(strict_types = 1);

namespace app\controllers\gql;

use app\schema\Types;
use Exception;
use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\ActiveController;

/**
 * Class GraphqlController
 * @package app\controllers\api
 */
class GraphqlController extends ActiveController {
	public $modelClass = '';

	/**
	 * @inheritdoc
	 */
	protected function verbs():array {
		return [
			'index' => ['POST', 'OPTIONS']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function actions():array {
		return [];
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function actionIndex():array {
		// сразу заложим возможность принимать параметры
		// как через MULTIPART, так и через POST/GET

		$query = Yii::$app->request->get('query', Yii::$app->request->post('query'));
		$variables = Yii::$app->request->get('variables', Yii::$app->request->post('variables'));
		$operation = Yii::$app->request->get('operation', Yii::$app->request->post('operation'));

		if (null === $query) {//multipart
			$rawInput = file_get_contents('php://input');
			$input = json_decode($rawInput, true);
			$query = ArrayHelper::getValue($input, 'query');
			$variables = ArrayHelper::getValue($input, 'variables', []);
			$operation = ArrayHelper::getValue($input, 'operation');
		}

		// библиотека принимает в variables либо null, либо ассоциативный массив
		// на строку будет ругаться

		if ([] !== $variables && !is_array($variables)) {
			try {
				$variables = Json::decode($variables);
			} /** @noinspection BadExceptionsProcessingInspection */ catch (Throwable $t) {
				$variables = null;
			}
		}

		// создаем схему и подключаем к ней наши корневые типы

		$schema = new Schema([
			'query' => Types::query(),
			'mutation' => Types::mutation(),
		]);

		// огонь!

		return GraphQL::executeQuery(
			$schema,
			$query,
			null,
			null,
			empty($variables)?null:$variables,
			empty($operation)?null:$operation
		)->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE);
	}
}
