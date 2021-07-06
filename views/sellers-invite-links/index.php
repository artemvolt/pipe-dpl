<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var SellerInviteLinkSearch $searchModel
 * @var string $modelName
 * @var ControllerTrait $controller
 * @var ActiveDataProvider $dataProvider
 */

use app\assets\ModalHelperAsset;
use app\models\seller\SellerInviteLink;
use app\models\seller\SellerInviteLinkSearch;
use kartik\grid\GridView;
use pozitronik\grid_config\GridConfig;
use pozitronik\helpers\Utils;
use pozitronik\traits\traits\ControllerTrait;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\web\JsExpression;
use yii\web\View;
use app\assets\ValidationAsset;

ModalHelperAsset::register($this);
ValidationAsset::register($this);
?>
<?= GridConfig::widget([
	'id' => "{$modelName}-index-grid",
	'grid' => GridView::begin([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'panel' => [
			'heading' => $this->title.(($dataProvider->totalCount > 0)?" (".Utils::pluralForm($dataProvider->totalCount, ['запись', 'записи', 'записей']).")":" (нет записей)"),
		],
		'summary' => null !== $searchModel?Html::a('Создать приглашение', $controller::to('create'), [
			'class' => 'btn btn-success',
			'onclick' => new JsExpression("AjaxModal('".$controller::to('create')."', '{$modelName}-modal-create-new');event.preventDefault();")
		]):null,
		'showOnEmpty' => true,
		'emptyText' => Html::a('Новая запись', $controller::to('create'), [
			'class' => 'btn btn-success',
			'onclick' => new JsExpression("AjaxModal('".$controller::to('create')."', '{$modelName}-modal-create-new');event.preventDefault();")
		]),
		'export' => false,
		'resizableColumns' => true,
		'responsive' => true,
		'columns' => [
			[
				'class' => ActionColumn::class,
				'template' => '{edit}{view}',
				'buttons' => [
					'edit' => static function(string $url, SellerInviteLink $model) use ($modelName):string {
						return Html::a('<i class="fa fa-edit"></i>', $url);
					},
					'view' => static function(string $url, SellerInviteLink $model) use ($modelName):string {
						return Html::a('<i class="fa fa-eye"></i>', $url);
					}
				],
			],
			'id',
			'store.name',
			'phone_number',
			'email',
			[
				'header' => 'Ссылка',
				'format' => 'raw',
				'value' => function(SellerInviteLink $link) {
					return Html::a("Ссылка", $link->inviteUrl());
				}
			],
			'expired_at:datetime'
		]
	])
]) ?>