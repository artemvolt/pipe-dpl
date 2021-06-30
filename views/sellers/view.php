<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Sellers $model
 */

use app\models\core\prototypes\ProjectConstants;
use app\models\seller\Sellers;
use pozitronik\filestorage\widgets\file_list\FileListWidget;
use pozitronik\widgets\BadgeWidget;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\widgets\DetailView;
use app\models\seller\active_record\SellersAR;

?>

<?= DetailView::widget([
	'model' => $model,
	'attributes' => [
		'create_date',
		'update_date',
		[
			'attribute' => 'currentStatusId',
			'value' => static function(Sellers $model) {
				return $model->currentStatus->name??null;
			},
		],
		'surname',
		'name',
		'patronymic',
		[
			'attribute' => 'gender',
			'value' => static function(Sellers $model) {
				return ArrayHelper::getValue(ProjectConstants::GENDER, $model->gender);
			},
		],
		[
			'attribute' => 'userId',
			'value' => static function(Sellers $model) {
				return $model->relatedUser->id??null;
			}
		],
		[
			'attribute' => 'userLogin',
			'value' => static function(Sellers $model) {
				return $model->relatedUser->login??null;
			}
		],
		[
			'attribute' => 'userEmail',
			'value' => static function(Sellers $model) {
				return $model->relatedUser->email??null;
			}
		],
		[
			'attribute' => 'stores',
			'format' => 'raw',
			'value' => static function(SellersAR $model):string {
				return BadgeWidget::widget([
					'items' => $model->stores,
					'subItem' => 'name'
				]);
			}
		],
		[
			'attribute' => 'dealers',
			'format' => 'raw',
			'value' => static function(SellersAR $model):string {
				return BadgeWidget::widget([
					'items' => $model->dealers,
					'subItem' => 'name'
				]);
			}
		],
		'birthday',
		[
			'attribute' => 'citizen',
			'value' => static function(SellersAR $model) {
				return $model->refCountry->name??null;
			}
		],
		'entry_date',
		[
			'attribute' => 'passport',
			'value' => static function(Sellers $model) {
				return "{$model->passport_series} {$model->passport_number}";
			}
		],
		'passport_whom',
		'passport_when',
		[
			'attribute' => 'relAddress',
			'value' => static function(Sellers $model) {
				return $model->relAddress->addressString??null;
			}
		],
		'keyword',
		'is_wireman_shpd:boolean',
		'inn',
		'snils',
		'deleted:boolean',
		[
			'attribute' => 'sellerDocs',
			'format' => 'raw',
			'value' => static function(Sellers $model):string {
				return FileListWidget::widget([
					'model' => $model,
					'tags' => [
						'passportScan', 'passportSelfie', 'passportTranslation', 'migrationCard', 'placeOfStay',
						'patent', 'residence', 'temporaryResidence', 'visa'
					],
					'allowVersions' => false
				]);
			}
		]
	]
]) ?>
