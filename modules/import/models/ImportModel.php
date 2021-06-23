<?php
declare(strict_types = 1);

namespace app\modules\import\models;

use app\modules\import\models\active_record\Import;
use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use pozitronik\filestorage\models\FileStorage;
use pozitronik\filestorage\traits\FileStorageTrait;
use pozitronik\helpers\ArrayHelper;
use Throwable;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * Class ImportModel
 * @property mixed $importFile атрибут загрузки файла
 * @property null|int $domain домен импорта (метка загрузки)
 * @property int $skipRows количество пропускаемых строк от начала
 * @property bool $skipEmptyRows пропускать ли пустые строки
 * @property int $importChunkSize количество импортируемых записей, обрабатываемых за раз
 * @property array $mappingRules правила соответствия полей в формате
 * @property-read ?string $filename путь загруженного/обрабатываемого импорта
 * @property-read int $count количество прогруженных строк
 * @property-read int $done количество импортированных строк
 * @property-read int $percent процент импортированных строк
 * @property-read int $errorCount количество строк с ошибкой импорта
 */
class ImportModel extends Model {
	use FileStorageTrait;

	/**
	 * @var string|null ActiveRecord-модель, в которую производится импорт
	 */
	public ?string $model = null;
	/**
	 * @var mixed $importFile атрибут загрузки файла
	 */
	public $importFile;
	/**
	 * правила соответствия полей
	 * @var array $mappingRules
	 */
	public array $mappingRules = [];

	/**
	 * @var int|null $domain домен импорта (метка загрузки)
	 */
	public ?int $domain = null;

	/**
	 * @var int $skipRows количество пропускаемых строк от начала
	 */
	public int $skipRows = 0;

	/**
	 * @var bool $skipEmptyRows пропускать ли пустые строки
	 */
	public bool $skipEmptyRows = true;

	/**
	 * @var int $importChunkSize количество импортируемых записей, обрабатываемых за раз
	 */
	public int $importChunkSize = 100;

	/**
	 * @var string|null $_filename Имя загруженного файла в локальной ФС
	 */
	private ?string $_filename;

	/**
	 * @var int|null $_count количество прогруженных строк
	 */
	private ?int $_count = null;

	/**
	 * @inheritDoc
	 */
	public function rules():array {
		return [
			[['skipRows'], 'integer'],
			[['domain'], 'default', 'value' => time()],
			[['skipEmptyRows'], 'boolean'],
			[['model'], 'string']
		];
	}

	/**
	 * @inheritDoc
	 */
	public function init():void {
		$this->domain = $this->domain??time();//валидатор не валидирует?
	}

	/**
	 * @return array
	 * @throws Exception
	 * @noinspection BadExceptionsProcessingInspection
	 */
	private function loadXls():array {
		try {
			$reader = new Xlsx();
			$reader->setReadDataOnly(true);
			$reader->setReadDataOnly(true);
			$spreadsheet = $reader->load($this->filename);
			$spreadsheet->setActiveSheetIndex(0);
			return $spreadsheet->getActiveSheet()->toArray(null, false);
		} catch (Throwable $t) {
			throw new Exception('Формат файла не поддерживается. Ошибка '.$t->getMessage());
		}
	}

	/**
	 * Импорт во временную таблицу
	 * @return bool
	 * @throws Exception
	 */
	public function preload():bool {
		$dataArray = $this->loadXls();
		$dataArray = array_slice($dataArray, $this->skipRows);
		if ($this->skipEmptyRows) {
			$dataArray = array_filter($dataArray);//ignore empty rows
		}
		$dataArray = array_map(function($row) {
			return [
				'data' => serialize(array_map("trim", $row)),
				'domain' => $this->domain,
				'model' => $this->model
			];
		}, $dataArray);
		$this->_count = Yii::$app->db->createCommand()->batchInsert(Import::tableName(), ['data', 'domain', 'model'], $dataArray)->execute();
		return true;
	}

	/**
	 * @param-out array $messages
	 * @param array $messages
	 * @return bool
	 * @throws Throwable
	 * todo: добавить правило, разрешающее скипать существующие данные
	 */
	public function import(array &$messages = []):bool {
		/** @var Import $data */
		if ([] === $data = Import::find()->where(['domain' => $this->domain, 'processed' => Import::NOT_PROCESSED])->limit($this->importChunkSize)->all()) {
			return true;
		}
		foreach ($data as $importRecord) {
			$importRow = unserialize($importRecord->data, ['allowed_classes' => false]);
			$mappedColumnData = [];
			foreach ($importRow as $columnIndex => $value) {
				/** @var array $currentRule */
				if ((null === $currentRule = ArrayHelper::getValue($this->mappingRules, $columnIndex)) || !is_array($currentRule)) continue;

				//есть функция переопределения вставки
				if ((null !== $foreignMatch = ArrayHelper::getValue($currentRule, 'foreign.match')) && is_callable($foreignMatch) && null !== $matchedValue = $foreignMatch($value)) {//функция нашла совпадение, вернула значение
					$value = $matchedValue;
				} elseif ((null !== $foreignClass = ArrayHelper::getValue($currentRule, 'foreign.class'))
					&& null !== $foreignModel = self::addInstance($foreignClass, [
						ArrayHelper::getValue($currentRule, 'foreign.attribute', new Exception('Foreign attribute parameter is required')) => $value
					])
				) {
					$value = (null === $return = ArrayHelper::getValue($currentRule, 'foreign.key'))?$foreignModel->primaryKey:$foreignModel->$return;
				}
				$mappedColumnData[$currentRule['attribute']] = $value;

			}
			$errors = [];
			if (null !== self::addInstance($this->model, $mappedColumnData, null, false, $errors)) {
				$importRecord->processed = Import::PROCESSED;
				$importRecord->save();
			} else {
				$importRecord->processed = Import::PROCESSED_ERROR;
				$importRecord->save();
				$messages[] = $errors;
			}

		}
		return false;
	}

	/**
	 * @param string $class
	 * @param array $searchCondition
	 * @param array|null $fields
	 * @param bool $forceUpdate
	 * @param array $errors
	 * @return ActiveRecord|null
	 */
	private static function addInstance(string $class, array $searchCondition, ?array $fields = null, bool $forceUpdate = false, array &$errors = []):?ActiveRecord {
		/** @var ActiveRecord $class */
		$instance = $class::find()->where($searchCondition)->one();
		$instance = $instance??new $class();
		if ($instance->isNewRecord || $forceUpdate) {
			$instance->load($fields??$searchCondition, '');
			if (!$instance->save()) {
				$errors = $instance->errors;
				return null;
			}
		}
		return $instance;
	}

	/**
	 * Подчищаем обработанные данные
	 */
	public function clear():void {
		Import::deleteAll(['model' => $this->model, 'domain' => $this->domain, 'processed' => Import::PROCESSED]);
	}

	/**
	 * @return string|null
	 * @throws Throwable
	 */
	public function getFilename():?string {
		if (null !== $lastFileName = ArrayHelper::getValue($this->files(['importFile']), 0)) {
			/** @var FileStorage $lastFileName */
			return $lastFileName->path;
		}
		return null;
	}

	/**
	 * @return int
	 */
	public function getCount():int {
		if (null === $this->_count) {
			$this->_count = (int)Import::find()->where(['model' => $this->model, 'domain' => $this->domain])->count();
		}
		return $this->_count;
	}

	/**
	 * @return float
	 */
	public function getPercent():float {
		return (int)(($this->done / $this->count) * 100);
	}

	/**
	 * @return int
	 */
	public function getDone():int {
		return (int)Import::find()->where(['model' => $this->model, 'domain' => $this->domain, 'processed' => [Import::PROCESSED, Import::PROCESSED_ERROR]])->count();
	}

	/**
	 * @return int
	 */
	public function getErrorCount():int {
		return (int)Import::find()->where(['model' => $this->model, 'domain' => $this->domain, 'processed' => Import::PROCESSED_ERROR])->count();
	}

}