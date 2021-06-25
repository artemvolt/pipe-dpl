<?php
declare(strict_types = 1);

namespace app\components\db;

use pozitronik\helpers\ArrayHelper;
use Throwable;
use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\db\Exception as DbException;
use yii\bootstrap4\ActiveForm;
use yii\db\Transaction;

/**
 * Trait ActiveRecordTrait
 * Попытка переделать правильно трейт с вспомогательными функциями ActiveRecord-классов
 */
trait ActiveRecordTrait {

	/**
	 * @return ActiveQuery
	 */
	public static function find():ActiveQuery {
		return new ActiveQuery(static::class);
	}

	/**
	 * По (int)$pk|(string)$pk пытается вернуть соответствующую ActiveRecord-модель
	 * @param string|ActiveRecordInterface $className
	 * @param int|string|ActiveRecordInterface $model
	 * @return ActiveRecordInterface|null
	 */
	public static function ensureModel($className, $model):?ActiveRecordInterface {
		if (is_string($model) && is_numeric($model)) {
			$model = (int)$model;
		}
		if (is_int($model)) {
			/** @var ActiveRecordInterface $className */
			$model = $className::findOne($model);
		}
		return is_a($model, ActiveRecordInterface::class, false)?$model:null;
	}

	/**
	 * @inheritDoc
	 * @param ActiveRecordInterface|int|string $model the model to be linked with the current one.
	 */
	public function link($name, $model, $extraColumns = []):void {
		/** @noinspection PhpMultipleClassDeclarationsInspection
		 * parent всегда будет ссылаться на BaseActiveRecord, но у нас нет способа это пометить
		 */
		parent::link($name, self::ensureModel($this->$name, $model), $extraColumns);
	}

	/**
	 * @param null|array $errors Возвращаемый список ошибок. null, чтобы не инициализировать на входе.
	 * @param null|bool $AJAXErrorsFormat Формат возврата ошибок: true: для ajax-валидации, false - as is, null (default) - в зависимости от типа запроса
	 * @param array $relationAttributes Массив с перечисление relational-моделей, приходящих отдельной формой
	 * @return null|bool true: модель сохранена, false: модель не сохранена, null: постинга не было
	 * @throws DbException
	 * @param-out array $errors На выходе всегда будет массив
	 */
	public function createModelFromPost(array &$errors = [], ?bool $AJAXErrorsFormat = null, array $relationAttributes = []):?bool {
		$errors = [];
		if ($this->load(Yii::$app->request->post())) {
			/**
			 * Все изменения заключаются в транзакцию с тем, чтобы откатывать сохранения записей, задаваемых в relational-атрибутах
			 * @var Transaction $transaction
			 */
			$transaction = static::getDb()->beginTransaction();
			/**
			 * Если сохранение одной модели завязано на сохранение другой модели, привязанной через relational-атрибут,
			 * то пытаемся сохранить связанную модель, при неудаче - откатываемся.
			 */
			if ([] !== $relationAttributes) {
				foreach ($relationAttributes as $relationAttributeName) {
					if ($this->hasProperty($relationAttributeName) && $this->canSetProperty($relationAttributeName)
						&& false === $this->$relationAttributeName->createModelFromPost($errors, $AJAXErrorsFormat)) {
						/*Ошибка сохранения связанной модели, откатим изменения*/
						$transaction->rollBack();
						return false;
					}
				}
			}

			if (false !== $result = $this->save()) {
				$transaction->commit();
			} else {
				if (null === $AJAXErrorsFormat) $AJAXErrorsFormat = Yii::$app->request->isAjax;
				/** @var ActiveRecord $this */
				$errors = $AJAXErrorsFormat
					?ActiveForm::validate($this)
					:$this->errors;
				$transaction->rollBack();
			}

			return $result;
		}
		return null;
	}

	/**
	 * Валидация для ajax-validation
	 * @return null|array
	 * @throws Throwable
	 */
	public function validateModelFromPost():?array {
		if ($this->load(Yii::$app->request->post())) {
			/** @var ActiveRecord $this */
			return ActiveForm::validate($this);
		}
		return null;
	}

	/**
	 * @param null|array $errors Возвращаемый список ошибок. null, чтобы не инициализировать на входе.
	 * @param null|bool $AJAXErrorsFormat Формат возврата ошибок: true: для ajax-валидации, false - as is, null (default) - в зависимости от типа запроса
	 * @param array $relationAttributes Массив с перечисление relational-моделей, приходящих отдельной формой
	 * @return null|bool true: модель сохранена, false: модель не сохранена, null: постинга не было
	 * @throws DbException
	 * @throws Throwable
	 * @param-out array $errors На выходе всегда будет массив
	 */
	public function updateModelFromPost(array &$errors = [], ?bool $AJAXErrorsFormat = null, array $relationAttributes = []):?bool {
		/* Методы совпадают, но оставлено на будущее */
		return $this->createModelFromPost($errors, $AJAXErrorsFormat, $relationAttributes);
	}

	/**
	 * Универсальная функция удаления любой модели
	 */
	public function safeDelete():void {
		if ($this->hasAttribute('deleted')) {
			$this->setAndSaveAttribute('deleted', !$this->deleted);
			$this->afterDelete();
		} else {
			$this->delete();
		}
	}

	/**
	 * Работает аналогично saveAttribute, но сразу сохраняет данные
	 * Отличается от updateAttribute тем, что триггерит onAfterSave
	 * @param string $name
	 * @param mixed $value
	 */
	public function setAndSaveAttribute(string $name, $value):void {
		$this->setAttribute($name, $value);
		$this->save();
	}

	/**
	 * Работает аналогично saveAttributes, но сразу сохраняет данные
	 * Отличается от updateAttributes тем, что триггерит onAfterSave
	 * @param null|array $values
	 */
	public function setAndSaveAttributes(?array $values):void {
		$this->setAttributes($values, false);
		$this->save();
	}

	/**
	 * Разница изменений атрибутов после обновления модели
	 * @param bool $strict Строгое сравнение
	 * @return array
	 * @throws Throwable
	 */
	public function identifyUpdatedAttributes(bool $strict = true):array {
		$changedAttributes = [];
		foreach ($this->attributes as $name => $value) {
			/** @noinspection TypeUnsafeComparisonInspection */
			$changed = $strict?(ArrayHelper::getValue($this, "oldAttributes.$name") !== $value):(ArrayHelper::getValue($this, "oldAttributes.$name") != $value);
			if ($changed) $changedAttributes[$name] = $value;//Нельзя использовать строгое сравнение из-за преобразований БД
		}
		return $changedAttributes;
	}

	/**
	 * Изменилось ли значение атрибута после обновления модели
	 * @param string $attribute
	 * @param bool $strict Строгое сравнение
	 * @return bool
	 * @throws Throwable
	 */
	public function isAttributeUpdated(string $attribute, bool $strict = true):bool {
		/** @noinspection TypeUnsafeComparisonInspection */
		return $strict?(ArrayHelper::getValue($this, "oldAttributes.$attribute") !== $this->$attribute):(ArrayHelper::getValue($this, "oldAttributes.$attribute") != $this->$attribute);
	}

	/**
	 * Если модель с текущими атрибутами есть - вернуть её. Если нет - создать и вернуть.
	 * @param array $attributes
	 * @return static
	 */
	public static function Upsert(array $attributes):self {
		if (null === $model = self::find()->where($attributes)->one()) {
			$model = new self();
			$model->load($attributes, '');
			$model->save();
		}
		return $model;
	}

}