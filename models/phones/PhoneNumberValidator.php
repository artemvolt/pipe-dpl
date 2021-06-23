<?php
declare(strict_types = 1);

namespace app\models\phones;

use yii\validators\Validator;

/**
 * Class PhoneNumberValidator
 * Валидатор телефонных номеров
 */
class PhoneNumberValidator extends Validator {
	/**
	 * @inheritDoc
	 */
	public function validateAttribute($model, $attribute):void {
		$validatedValue = $model->$attribute;
		if (is_array($validatedValue)) {
			$errors = [];
			foreach ($validatedValue as $phone) {
				if (!Phones::isValidNumber($phone)) {
					$errors[] = $phone;
				}
			}
			if ([] !== $errors) {
				$this->addError($model, $attribute, (1 === count($errors))
					?implode(', ', $errors)." не является корректным телефонным номером"
					:implode(', ', $errors)." не являются корректными телефонными номерами");
			}
		} elseif (!Phones::isValidNumber($validatedValue)) $this->addError($model, $attribute, "$validatedValue не является корректным телефонным номером");
	}
}