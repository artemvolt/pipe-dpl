<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\sys\permissions\filters\PermissionFilter;
use app\widgets\search\SearchHelper;
use app\widgets\search\SearchWidget;
use pozitronik\sys_options\models\SysOptions;
use pozitronik\traits\traits\ControllerTrait;
use Yii;
use yii\base\UnknownPropertyException;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class AjaxController
 */
class AjaxController extends Controller {
	use ControllerTrait;

	/**
	 * {@inheritDoc}
	 */
	public function init():void {
		parent::init();
		$this->enableCsrfValidation = false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function behaviors():array {
		return [
			[
				'class' => ContentNegotiator::class,
				'formats' => [
					'application/json' => Response::FORMAT_JSON
				]
			],
			'access' => [
				'class' => PermissionFilter::class
			]
		];
	}

	/**
	 * @param string $alias
	 * @param string|null $term
	 * @return string[][]
	 * @throws UnknownPropertyException
	 */
	public function actionSearch(string $alias, ?string $term):array {
		if (null !== $ARClass = ArrayHelper::getValue(Yii::$app, "params.searchConfig.{$alias}.class")) {
			return SearchHelper::Search(
				$ARClass,
				$term,
				ArrayHelper::getValue(Yii::$app, "params.searchConfig.{$alias}.limit", SearchWidget::DEFAULT_LIMIT),
				ArrayHelper::getValue(Yii::$app, "params.searchConfig.{$alias}.attributes"));
		}
		return [];
	}

	/**
	 * Применение настроек
	 * @return array
	 */
	public function actionSetSystemOption():array {
		$valueType = Yii::$app->request->post('type', 'string');
		switch ($valueType) {//JS types
			default:
			case 'string':
				$value = (string)Yii::$app->request->post('value');
			break;
			case 'boolean':
				$value = 'true' === Yii::$app->request->post('value');
			break;
			case 'number':
				$value = (int)Yii::$app->request->post('value');
			break;
		}

		return [
			'success' => SysOptions::setStatic(Yii::$app->request->post('name'), $value)
		];
	}

}
