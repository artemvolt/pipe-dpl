<?php
declare(strict_types = 1);

namespace app\widgets\selectmodelwidget;

use kartik\select2\Select2;
use pozitronik\helpers\ArrayHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\bootstrap4\Html;
use yii\web\JsExpression;

/**
 * Class SelectModelWidget.
 * Надстройка над Select2, работающая с кастомизированными ActiveRecord-моделями (в основном -- справочниками).
 *
 * @property string $selectModelClass Класс модели, по которой будем выбирать данные
 * @property-read ActiveRecordInterface $selectModel Загруженная модель
 *
 * @property ActiveRecord $model Перекрываем описание атрибута модели
 * @property array $exclude Записи, исключаемые из выборки. Массив id, либо массив элементов
 * @property ActiveQuery $selectionQuery Переопределение запроса, если нужны какие-то модификации, но не нужно передавать данные в $data
 * @property string $mapAttribute Названия атрибута, который будет отображаться на выбиралку
 * @property string $searchAttribute Поле по, которому будет производиться поиск. Если не передан, то поиск будет
 * производиться по $mapAttribute
 * @property string $concatFields Список полей для конкатенации ответа. Например, ФИО хранится в 3 полей, ищем по
 * фамилию. Поиск должен вернуть только фамилию, но передав тут 'surname, name, patronymic', получим полное ФИО
 * @property string|null $pkName Имя ключевого атрибута модели, если не указано -- подберётся автоматически
 * @property int $ajaxMinimumInputLength Количество символов для старта поиска при аксовом режиме
 * @property string $ajaxSearchUrl Путь к экшену ajax-поиска.
 * @property int $loadingMode self::DATA_MODE_AJAX -- фоновая загрузка, DATA_MODE_LOAD -- вычисляемые данные
 * @property bool $multiple true by default
 *
 * @property string $jsPrefix костыль для призыва нужных JS-функций в ассетах потомков
 * @property false|string $dataOptions название метода используемого класса, возвращающего дополнительные опции для выбиралки. Если false -- то игнорируется
 */
class SelectModelWidget extends Select2 {
	public const DATA_MODE_LOAD = 0;//данные прогружаются сразу
	public const DATA_MODE_AJAX = 1;//данные прогружаются аяксовым поиском

	//private $data = [];//calculated/evaluated/received data array
	private $ajaxPluginOptions = [];//calculated select2 ajax parameters
	/** @var ActiveRecordInterface $_selectModel */
	protected $_selectModel;

	public $pkName;//primary key name for selectModel
	public $selectModelClass;
	public $selectionQuery;
	public $exclude = [];
	public $mapAttribute = 'name';
	public $searchAttribute;
	public $concatFields;
	public $ajaxMinimumInputLength = 1;
	public $ajaxSearchUrl;

	public $loadingMode = self::DATA_MODE_LOAD;
	public $multiple = true;//alias of pluginOptions['multiple']
	public $jsPrefix = '';
	public $data = [];//required initialization
	public $value = [];//required initialization
	public $dataOptions = 'dataOptions';

	/**
	 * При AJAX-загрузке отображаемые данные будут отображаться согласно логике, вшитой в Select2 - из initValueText (см. Select2::$initValueText).
	 * Виджет попытается сгенерировать нужный набор данных прямо из модели/из $data, в случае необходимости
	 * @return string|array
	 */
	private function initAjaxValueText() {
		if (null !== $this->initValueText) return $this->initValueText;//параметр задан через конфигурацию, ничего вычислять не надо
		/*Вычисляем весь скоуп данных с фильтрацией по текущему значению*/
		$this->initData($this->value);
		return $this->data;
	}

	/**
	 * AJAX parameters generator
	 */
	private function initAjax():void {
		if ($this->searchAttribute) {
			$column = "column: '{$this->searchAttribute}', ";
		} else {
			$column = 'name' === $this->mapAttribute?null:"column: '{$this->mapAttribute}', ";
		}
		$concat = $this->concatFields?"concatFields: '{$this->concatFields}', ":null;
		$this->ajaxPluginOptions = [
			'minimumInputLength' => $this->ajaxMinimumInputLength,
			'initValueText' => $this->initAjaxValueText(),
			'ajax' => [
				'url' => $this->ajaxSearchUrl,
				'dataType' => 'json',
				'data' => new JsExpression("function(params) { return {term:params.term, ".
					$column.$concat."page: params.page}; }"),
				'cache' => true
			]
		];
	}

	/**
	 * Генерирует набор данных для подстановки в выбиралку без загрузки. При self::DATA_MODE_AJAX используется для подстановки данных в уже имеющиеся значения (см. self::initAjaxValueText())
	 * @param $filterValue - если указано, то выборка скукожится только до переданного значения
	 */
	private function initData($filterValue = null):void {
		if ([] === $this->data) {
			if (null === $this->selectionQuery) $this->selectionQuery = $this->_selectModel::find();
			if (is_array($this->exclude) && [] !== $this->exclude) {
				if ($this->exclude[0] instanceof ActiveRecordInterface) {
					$this->exclude = ArrayHelper::getColumn($this->exclude, $this->pkName);
				}
				$this->selectionQuery->andWhere(['not in', $this->pkName, $this->exclude]);
			}

			if (null !== $filterValue) {
				if (is_array($filterValue)) {
				$this->selectionQuery->andWhere(['in', $this->pkName, $filterValue]);
				} else {
					$this->selectionQuery->andWhere(['id' => $filterValue]);
				}
			}
			$this->data = ArrayHelper::map($this->selectionQuery->all(), $this->pkName, $this->mapAttribute);
		}
	}

	/**
	 * @return ActiveRecordInterface
	 * @throws InvalidConfigException
	 */
	public function getSelectModel():ActiveRecordInterface {
		if (null !== $this->_selectModel) return $this->_selectModel;
		$this->_selectModel = Yii::createObject($this->selectModelClass);
		if (!($this->_selectModel instanceof ActiveRecordInterface)) {
			throw new InvalidConfigException("{$this->selectModel} must be a instance of ActiveRecordInterface");
		}
		return $this->_selectModel;
	}

	/**
	 * Функция инициализации и нормализации свойств виджета
	 */
	public function init():void {
		parent::init();
		SelectModelWidgetAssets::register($this->getView());

		$this->pkName = $this->pkName??$this->selectModel::primaryKey()[0];
		if (null === $this->pkName) {
			throw new InvalidConfigException("{$this->selectModel} must have primary key and it should not be composite");
		}

		$this->options['id'] = isset($this->options['id'])?$this->options['id'].$this->model->primaryKey:Html::getInputId($this->model, $this->attribute).$this->model->primaryKey;

		/*В зависимости от режима работы AJAX/LOAD настраиваем виджет и генерируем выводимый список*/
		switch ($this->loadingMode) {
			default:
			case self::DATA_MODE_LOAD:
				$this->initData();
			break;
			case self::DATA_MODE_AJAX:
				$this->initAjax();
			break;
		}

		if ($this->dataOptions && method_exists($this->selectModel, $this->dataOptions)) {//если у модели есть опции для выбиралки, присунем их к стандартным опциям
			$pKeys = array_keys($this->data);
			$options = ArrayHelper::getValue($this->options, 'options', []);
			if (!is_array($options)) throw new InvalidConfigException("Options must be an array");
			/** @var array $options */
			$this->options['options'] = ArrayHelper::merge($options, $this->selectModel::{$this->dataOptions}($pKeys));
		}

		$this->pluginOptions = [
				'allowClear' => true,
				'multiple' => $this->multiple,
				'language' => 'ru',
				'templateResult' => new JsExpression('function(item) {return '.$this->jsPrefix.'TemplateResult(item)}'),
				'escapeMarkup' => new JsExpression('function(markup) {return '.$this->jsPrefix.'EscapeMarkup(markup);}'),
//				'matcher' => new JsExpression('function(params, data) {return '.$this->jsPrefix.'MatchCustom(params, data)}')
			] + $this->ajaxPluginOptions + $this->pluginOptions;
	}

}
