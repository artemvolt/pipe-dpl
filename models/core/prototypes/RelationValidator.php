<?php
declare(strict_types = 1);

namespace app\models\core\prototypes;

use pozitronik\helpers\ArrayHelper;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\validators\Validator;

/**
 * Class RelationValidator
 */
class RelationValidator extends Validator {
	/**
	 * @inheritDoc
	 * @param ActiveRecord $model
	 * @param $attribute
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public function validateAttribute($model, $attribute):void {
		if (isset($model->relatedRecords[$attribute]) && null !== $relation = $model->getRelation($attribute)) {
			if ($relation->multiple) {
				throw new InvalidConfigException("Sorry, that kind of relations is not supported yet");
			} else {
				$relation_fk = ArrayHelper::key($relation->link);
				$model_fk = $relation->link[$relation_fk];
				$model->$model_fk = $model->$attribute->$relation_fk;

				$model->clearErrors($model_fk);
				$model->validate($model_fk, false);
			}
		} else {
			$model->addError($attribute, get_class($this).' has no relation named '.$attribute);
		}
	}
}
