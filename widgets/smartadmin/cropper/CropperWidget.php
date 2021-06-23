<?php
declare(strict_types = 1);

namespace app\widgets\smartadmin\cropper;

use yii\bootstrap4\Html;
use yii\widgets\InputWidget;

/**
 * Class CropperWidget
 * @package app\widgets\smartadmin\cropper
 * @property array $pluginOptions Настройки, используемые для инициализации Cropper
 * @property string $imageId id <img> для подгрузки фото в кроппер
 * @property string $cropperUploadInputId id файлового input'а
 * @property string $cropperCropElementId id элемента, который будет триггерить кроппинг изображения и его отправку на сервак
 * @property null|string $modalId id модалки, на случай если кроппер завернут в неё.
 */
class CropperWidget extends InputWidget {
	/**
	 * @var array настройки, используемые для инициализации Cropper
	 * @see https://github.com/fengyuanchen/cropperjs
	 */
	public array $pluginOptions = [];
	/**
	 * @var string id <img> для подгрузки фото в кроппер
	 */
	public string $imageId = 'cropper__user-logo_img';
	/**
	 * @var string id файлового input'а
	 */
	public string $cropperUploadInputId = 'cropper__upload-input';
	/**
	 * @var string id элемента, который будет триггерить кроппинг изображения и его отправку на сервак
	 */
	public string $cropperCropElementId = 'cropper__crop';
	/**
	 * @var string|null ID модалки, на случай если кроппер завернут в неё.
	 * Требуется для корректной инициализации кроппера.
	 */
	public ?string $modalId = null;

	public function init():void {
		parent::init();
		CropperWidgetAsset::register($this->view);
	}

	public function run():string {
		$options = json_encode([
			'fileInputName' => Html::getInputName($this->model, $this->attribute),
			'imageId' => "#{$this->imageId}",
			'modalId' => $this->modalId?"#{$this->modalId}":null,
			'cropperUploadInputId' => "#{$this->cropperUploadInputId}",
			'cropperCropElementId' => "#{$this->cropperCropElementId}",
			'pluginOptions' => $this->pluginOptions
		]);

		$this->view->registerJs("cropperInitConfig.init({$options})");

		return $this->render('main', ['widget' => $this]);
	}
}