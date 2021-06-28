<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\controllers\SellersController;
use app\models\common\traits\CreateAddressTrait;
use app\models\seller\active_record\SellersAR;
use app\models\common\traits\CreateAccessTrait;
use pozitronik\filestorage\traits\FileStorageTrait;
use yii\helpers\ArrayHelper;

/**
 * Class Sellers
 * Конкретный продавец
 * @property mixed $passportTranslation Скан нотариально заверенного перевода (в случае если нет транскрипции на
 * кириллицу)
 * @property mixed $migrationCard Миграционная карта (всем, кроме граждан Беларуси)
 * @property mixed $placeOfStay Отрывная часть бланка к уведомлению о прибытии в место пребывания
 * @property mixed $patent Патент + квитанция об оплате
 * @property mixed $residence Вид на жительство
 * @property mixed $temporaryResidence Разрешение на временное проживание
 * @property mixed $visa Виза
 *
 * @property string $urlToEntity
 */
class Sellers extends SellersAR {
	use FileStorageTrait;
	use CreateAccessTrait;
	use CreateAddressTrait;

	public $passportTranslation;
	public $migrationCard;
	public $placeOfStay;
	public $patent;
	public $residence;
	public $temporaryResidence;
	public $visa;

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return ArrayHelper::merge(parent::attributeLabels(), [
			'sellerDocs' => 'Сканы',
			'passportTranslation' => 'Скан нотариально заверенного перевода',
			'migrationCard' => 'Миграционная карта',
			'placeOfStay' => 'Отрывная часть бланка к уведомлению о прибытии в место пребывания',
			'patent' => 'Патент + квитанция об оплате',
			'residence' => 'Вид на жительство',
			'visa' => 'Виза',
			'temporaryResidence' => 'Разрешение на временное проживание'
		]);
	}

	/**
	 * URL для нахождения продавца по ID
	 * @return string
	 */
	public function getUrlToEntity():string {
		return SellersController::to('index', ['SellersSearch[id]' => $this->id], true);
	}

}