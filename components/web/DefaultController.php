<?php
declare(strict_types = 1);

namespace app\components\web;

use app\components\db\ActiveRecordTrait;
use app\models\core\prototypes\EditableFieldAction;
use app\models\sys\permissions\filters\PermissionFilter;
use app\models\sys\permissions\traits\ControllerPermissionsTrait;
use app\modules\import\models\ImportAction;
use app\modules\import\models\ProcessImportAction;
use pozitronik\helpers\ControllerHelper;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UnknownClassException;
use yii\db\ActiveRecord;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class DefaultController
 * Все контроллеры и все вью плюс-минус одинаковые, поэтому можно сэкономить на прототипировании
 * @property string $modelClass Модель, обслуживаемая контроллером
 * @property string $modelSearchClass Поисковая модель, обслуживаемая контроллером
 * @property bool $enablePrototypeMenu Включать ли контроллер в меню списка прототипов
 * @property array $mappingRules Настройки параметров импорта
 *
 * @property-read ActiveRecord $searchModel
 * @property-read ActiveRecord|ActiveRecordTrait $model
 */
class DefaultController extends Controller {
	use ControllerPermissionsTrait;

	protected const DEFAULT_TITLE = null;

	/**
	 * @var string $modelClass
	 */
	public string $modelClass;
	/**
	 * @var string $modelSearchClass
	 */
	public string $modelSearchClass;

	/**
	 * @var bool enablePrototypeMenu
	 */
	public bool $enablePrototypeMenu = true;

	/**
	 * @return array
	 */
	public function getMappingRules():array {
		return [];
	}

	/**
	 * @return string
	 */
	public static function Title():string {
		return static::DEFAULT_TITLE??ControllerHelper::ExtractControllerId(static::class);
	}

	/**
	 * @inheritDoc
	 */
	public function beforeAction($action):bool {
		$this->view->title = static::DEFAULT_TITLE??($this->view->title??$this->id);
		if (!isset($this->view->params['breadcrumbs'])) {
			if ($this->defaultAction === $action->id) {
				$this->view->params['breadcrumbs'][] = $this->id;
			} else {
				$this->view->params['breadcrumbs'][] = ['label' => $this->defaultAction, 'url' => $this::to($this->defaultAction)];
				$this->view->params['breadcrumbs'][] = $action->id;
			}

		}
		return parent::beforeAction($action);
	}

	/**
	 * @inheritDoc
	 */
	public function behaviors():array {
		return [
			[
				'class' => ContentNegotiator::class,
				'only' => ['ajax-search'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON
				]
			],
			[
				'class' => AjaxFilter::class,
				'only' => ['ajax-search']
			],
			'access' => [
				'class' => PermissionFilter::class
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	public function actions():array {
		return ArrayHelper::merge(parent::actions(), [
			'editAction' => [
				'class' => EditableFieldAction::class,
				'modelClass' => $this->modelClass,
			],
			'import' => [
				'class' => ImportAction::class,
				'modelClass' => $this->modelClass
			],
			'process-import' => [
				'class' => ProcessImportAction::class,
				'mappingRules' => $this->mappingRules
			]
		]);
	}

	/**
	 * Генерирует меню для доступа ко всем контроллерам по указанному пути
	 * @param string $alias
	 * @return array
	 * @throws Throwable
	 * @throws UnknownClassException
	 */
	public static function MenuItems(string $alias = "@app/controllers"):array {
		$items = [];
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Yii::getAlias($alias)), RecursiveIteratorIterator::SELF_FIRST);
		/** @var RecursiveDirectoryIterator $file */
		foreach ($files as $file) {
			/** @var self $model */
			if ($file->isFile() && ('php' === $file->getExtension()) && (null !== $model = ControllerHelper::LoadControllerClassFromFile($file->getRealPath(), null, [self::class])) && $model->enablePrototypeMenu) {
				$items[] = [
					'label' => $model->id,
					'url' => [$model::to($model->defaultAction)],
					'visible' => $model::hasPermission($model->defaultAction)
				];
			}
		}
		return $items;
	}

	/**
	 * @inheritDoc
	 */
	public function getViewPath():string {
		return '@app/views/default';
	}

	/**
	 * @return ActiveRecord|ActiveRecordTrait
	 */
	public function getModel():ActiveRecord {
		return (new $this->modelClass());
	}

	/**
	 * @return ActiveRecord
	 */
	public function getSearchModel():ActiveRecord {
		return (new $this->modelSearchClass());
	}

	/**
	 * @return string
	 * @throws InvalidConfigException
	 * @noinspection PhpPossiblePolymorphicInvocationInspection
	 */
	public function actionIndex():string {
		$params = Yii::$app->request->queryParams;
		$searchModel = $this->searchModel;

		return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $searchModel->search($params),
				'controller' => $this,
				'modelName' => $this->model->formName()
			]
		);
	}

	/**
	 * @param int $id
	 * @return string
	 * @throws Throwable
	 */
	public function actionView(int $id):string {
		if (null === $model = $this->model::findOne($id)) {
			throw new NotFoundHttpException();
		}
		if (Yii::$app->request->isAjax) {
			return $this->renderAjax('modal/view', [
				'model' => $model
			]);
		}
		return $this->render('view', [
			'model' => $model
		]);
	}

	/**
	 * @param int $id
	 * @return string|Response
	 * @throws Throwable
	 */
	public function actionEdit(int $id) {
		if (null === $model = $this->model::findOne($id)) {
			throw new NotFoundHttpException();
		}

		/** @var ActiveRecordTrait $model */
		if (Yii::$app->request->post('ajax')) {/* запрос на ajax-валидацию формы */
			return $this->asJson($model->validateModelFromPost());
		}
		$errors = [];
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
			?$this->renderAjax('modal/edit', ['model' => $model])
			:$this->render('edit', ['model' => $model]);
	}

	/**
	 * @return string|Response
	 * @throws Throwable
	 */
	public function actionCreate() {
		$model = $this->model;
		if (Yii::$app->request->post('ajax')) {/* запрос на ajax-валидацию формы */
			return $this->asJson($model->validateModelFromPost());
		}
		$errors = [];
		$posting = $model->createModelFromPost($errors);/* switch тут нельзя использовать из-за его нестрогости */
		if (true === $posting) {/* Модель была успешно прогружена */
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
	 * @param int $id
	 * @return Response
	 * @throws Throwable
	 */
	public function actionDelete(int $id):Response {
		if (null === $model = $this->model::findOne($id)) {
			throw new NotFoundHttpException();
		}
		/** @noinspection PhpUndefinedMethodInspection */
		$model->safeDelete();
		return $this->redirect('index');
	}

	/**
	 * Аяксовый поиск в Select2
	 * @param string|null $term
	 * @param string $column
	 * @param string|null $concatFields Это список полей для конкатенации. Если этот параметр передан, то вернем
	 * результат CONCAT() для этих полей вместо поля параметра $column
	 * @return string[][]
	 * @throws ForbiddenHttpException
	 * @throws Throwable
	 */
	public function actionAjaxSearch(?string $term, string $column = 'name', string $concatFields = null):array {
		$out = [
			'results' => [
				'id' => '',
				'text' => ''
			]
		];
		if (null !== $term) {
			$tableName = $this->model::tableName();
			if ($concatFields) {
				// добавляем название таблицы перед каждым полем
				$concatFieldsArray = preg_filter('/^/', "{$tableName}.", explode(',', $concatFields));
				// создаем CONCAT() функцию. Формат: CONCAT(tableName.surname,' ',tableName. name)
				$textFields = 'CONCAT('.implode(",' ',", $concatFieldsArray).')';
			} else {
				$textFields = "{$tableName}.{$column}";
			}
			$data = $this->model::find()
				->select(["{$tableName}.id", "{$textFields} as text"])
				->where(['like', "{$tableName}.{$column}", "%$term%", false])
				->active()
				->distinct()
				->scope()
				->asArray()
				->all();
			$out['results'] = array_values($data);
		}
		return $out;
	}

}