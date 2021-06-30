<?php
declare(strict_types = 1);

namespace app\models\countries\active_record\references;

use pozitronik\references\models\CustomisableReference;
use pozitronik\references\models\ReferenceLoader;
use pozitronik\traits\traits\ModuleTrait;
use Yii;

/**
 * Class RefCountries
 *
 * @property int $is_homeland Этот флаг отвечает за упрощённую логику регистрации продавца с гражданством этой страны
 */
class RefCountries extends CustomisableReference {

	public string $menuCaption = "Справочник стран";

	public $moduleId = "Страны";

	/**
	 * @inheritdoc
	 */
	public function rules():array {
		return array_merge(parent::rules(), [
			[['is_homeland'], 'boolean']//теоретически, таких стран может быть много (например, упрощёнку введём для Беларуси).
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels():array {
		return array_merge(parent::attributeLabels(), [
			'is_homeland' => 'Упрощённые правила'
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function getColumns():array {
		$columns = parent::getColumns();
		$columns[] = 'is_homeland:boolean';
		return $columns;
	}

	/**
	 * @inheritDoc
	 */
	public function getForm():string {
		return '@app/views/countries/references/refcountries/_form.php';
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'ref_countries';
	}

	/**
	 * @return self[]
	 */
	public static function getHomelandCountries():array {
		return self::findAll(['is_homeland' => true]);
	}
}