<?php
declare(strict_types = 1);

/**
 * Шаблон главной страницы списка всех пользователей
 * @var View $this
 * @var RestDataProvider $dataProvider
 */

use app\assets\ModalHelperAsset;
use app\controllers\UsersController;
use app\models\sys\users\Users;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use pozitronik\grid_config\GridConfig;
use pozitronik\helpers\Utils;
use pozitronik\widgets\BadgeWidget;
use simialbi\yii2\rest\RestDataProvider;
use yii\web\JsExpression;
use yii\web\View;
use kartik\grid\GridView;
use yii\bootstrap4\Html;

ModalHelperAsset::register($this);
?>

<?= GridConfig::widget([
	'id' => 'users-index-grid',
	'grid' => GridView::begin([
		'dataProvider' => $dataProvider,
		'panel' => [
			'heading' => $this->title.(($dataProvider->totalCount > 0)?" (".Utils::pluralForm($dataProvider->totalCount, ['пользователь', 'пользователя', 'пользователей']).")":" (нет пользователей)"),
		],
		'summary' => Html::a('Новый пользователь', UsersController::to('create'), ['class' => 'btn btn-success summary-content']),
		'showOnEmpty' => true,
		'emptyText' => Html::a('Новый пользователь', UsersController::to('create'), ['class' => 'btn btn-success']),
		'toolbar' => false,
		'export' => false,
		'resizableColumns' => true,
		'responsive' => true,
		'columns' => [
			[
				'class' => ActionColumn::class,
				'template' => '{edit}',
				'buttons' => [
					'edit' => static function(string $url, Users $model) {
						return Html::a('<i class="fa fa-edit"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$model->formName()}-modal-edit-{$model->id}');event.preventDefault();")
						]);
					},
				],
			],
			'id',
			'username',
			'login',
			[
				'class' => DataColumn::class,
				'attribute' => 'create_date',
				'format' => 'datetime'
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'allUserPermission',
				'format' => 'raw',
				'value' => static function(Users $user) {
					return BadgeWidget::widget([
						'items' => $user->allPermissions(),
						'subItem' => 'name'
					]);
				}
			]
		]
	])
]) ?>