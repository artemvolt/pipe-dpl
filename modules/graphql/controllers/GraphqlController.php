<?php
declare(strict_types = 1);

namespace app\modules\graphql\controllers;

use app\modules\graphql\schema\types\Types;
use Exception;
use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\ActiveController;
use Throwable;

/**
 * Class GraphqlController
 * @package app\modules\history\controllers
 */
class GraphqlController extends ActiveController {
	public $modelClass = '';

	/**
	 * {@inheritdoc}
	 */
	protected function verbs():array {
		return [
			'index' => ['POST', 'OPTIONS']
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function actions():array {
		return [];
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function actionIndex():array {
		$query = Yii::$app->request->get('query', Yii::$app->request->post('query'));
		$variables = Yii::$app->request->get('variables', Yii::$app->request->post('variables'));
		$operation = Yii::$app->request->get('operation', Yii::$app->request->post('operation'));

		if (null === $query) {
			$rawInput = file_get_contents('php://input');
			$input = Json::decode($rawInput);
			$query = ArrayHelper::getValue($input, 'query');
			$variables = ArrayHelper::getValue($input, 'variables', []);
			$operation = ArrayHelper::getValue($input, 'operation');
		}

		if (!empty($variables) && !is_array($variables)) {
			try {
				$variables = Json::decode($variables);
			} catch (Throwable $t) {
				$variables = null;
			}
		}

		return GraphQL::executeQuery(
			new Schema([
				'query' => Types::query(),
				'mutation' => Types::mutation(),
			]),
			$query,
			null,
			null,
			$variables?:null,
			$operation?:null
		)->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE);
	}
}
