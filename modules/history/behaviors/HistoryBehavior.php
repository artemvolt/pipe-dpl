<?php
declare(strict_types = 1);

namespace app\modules\history\behaviors;

use app\modules\history\models\ActiveRecordHistory;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;
use yii\helpers\ArrayHelper;

/**
 * @property ActiveRecord $owner The owner of this behavior
 * @property array|null $isRelation Конфигурация связи в реляционных атрибутах. Позволяет указать, что это изменение нужно сохранять как изменение атрибута другой модели. Формат:
 *    [
 *        Model::class,//имя базовой модели, атрибуты которой хранятся в этой таблице/модели
 *        'modelKeyAttributeName',//имя атрибута модели, по которому производится связь со с базовой моделью
 *        'relationAttributeName',//имя атрибута модели, хранящего значение
 *        'modelRelatedAttributeName'//имя атрибута базовой модели, значение которого хранится в этой модели (опционально, если не задано, используется имя текущего класса)
 *    ]
 */
class HistoryBehavior extends Behavior {
	private const DISABLE = false;

	public $relations = [];
	public $isRelation;
	public $afterUpdate;

	/**
	 *
	 * @return array
	 * @throws InvalidConfigException
	 */
	private function getModelData():array {
		if (null !== $this->isRelation) {
			if (is_array($this->isRelation)) {
				/** @var ActiveRecord $class */
				[$class, $keyAttribute, $linkAttribute] = $this->isRelation;
				$linkedAttributeName = ArrayHelper::getValue($this->isRelation, 3, $this->owner->formName());
				if (null !== $model = $class::findOne($this->owner->$keyAttribute)) {
					return [$model, [$linkedAttributeName => ArrayHelper::getValue($this->owner, $linkAttribute)], $this->owner];
				}
			} elseif (is_callable($this->isRelation)) {
				return call_user_func($this->isRelation, $this);
			}
		}
		return [$this->owner, $this->owner->attributes, null];
	}

	/**
	 * {@inheritDoc}
	 */
	public function events():array {
		return self::DISABLE?[]:[
			ActiveRecord::EVENT_AFTER_INSERT => function(Event $event) {
				/** @var ActiveRecord $model */
				[$model, $attributes, $relation] = $this->getModelData();
				ActiveRecordHistory::push($model, [], $attributes, $relation, $event);
			},
			ActiveRecord::EVENT_AFTER_UPDATE => function(AfterSaveEvent $event) {
				if (is_callable($this->afterUpdate)) {//полностью переопределяем метод. Введено, как хак сохранения плохо тупо сделанных периодов. Нужно либо перепроектировать периоды, либо придумать логичную схему для правил
					call_user_func($this->afterUpdate, $event);
					return;
				}
				$newAttributes = [];
				/** @var ActiveRecord $model */
				[$model, , $relation] = $this->getModelData();
				foreach ($event->changedAttributes as $key => $value) {
					$newAttributes[$key] = $model->$key;
				}
				if ([] !== $newAttributes) ActiveRecordHistory::push($model, $event->changedAttributes, $newAttributes, $relation, $event);
			},
			ActiveRecord::EVENT_AFTER_DELETE => function(Event $event) {
				/** @var ActiveRecord $model */
				[$model, $attributes, $relation] = $this->getModelData();
				ActiveRecordHistory::push($model, $attributes, [], $relation, $event);
			}
		];
	}

}