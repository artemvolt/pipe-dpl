<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var ManagersSearch $searchModel
 * @var string $modelName
 * @var ControllerTrait $controller
 * @var ActiveDataProvider $dataProvider
 */

use app\assets\ModalHelperAsset;
use app\controllers\DealersController;
use app\controllers\StoresController;
use app\models\managers\ManagersSearch;
use kartik\datetime\DateTimePicker;
use kartik\grid\GridView;
use pozitronik\grid_config\GridConfig;
use pozitronik\helpers\Utils;
use pozitronik\traits\traits\ControllerTrait;
use pozitronik\widgets\BadgeWidget;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\web\JsExpression;
use yii\web\View;
use app\controllers\UsersController;

ModalHelperAsset::register($this);
?>
<?= GridConfig::widget([
	'id' => "{$modelName}-index-grid",
	'grid' => GridView::begin([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'panel' => [
			'heading' => $this->title.(($dataProvider->totalCount > 0)?" (".Utils::pluralForm($dataProvider->totalCount, ['запись', 'записи', 'записей']).")":" (нет записей)"),
		],
		'summary' => null !== $searchModel?Html::a('Новая запись', $controller::to('create'), [
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
					'edit' => static function(string $url, ManagersSearch $model) use ($modelName):string {
						return Html::a('<i class="fa fa-edit"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$modelName}-modal-edit-{$model->id}');event.preventDefault();")
						]);
					},
					'view' => static function(string $url, ManagersSearch $model) use ($modelName):string {
						return Html::a('<i class="fa fa-eye"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$modelName}-modal-view-{$model->id}');event.preventDefault();")
						]);
					},
					'edit-user' => static function(string $url, ManagersSearch $model) use ($modelName):string {
						return Html::a('<i class="fa fa-user-alt"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$modelName}-modal-edit-user-{$model->id}');event.preventDefault();")
						]);
					},
				],
			],
			'id',
			[
				'attribute' => 'userId',
				'format' => 'raw',
				'value' => static function(ManagersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relatedUser,
						'subItem' => 'id',
						'useBadges' => false,
						'urlScheme' => [
							UsersController::to(
								'index',
								['UsersSearch[id]' => $model->relatedUser->id??null]
							)
						]
					]);
				}
			],
			[
				'attribute' => 'userLogin',
				'format' => 'raw',
				'value' => static function(ManagersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relatedUser,
						'subItem' => 'login',
						'useBadges' => false
					]);
				}
			],
			[
				'attribute' => 'userEmail',
				'format' => 'raw',
				'value' => static function(ManagersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->relatedUser,
						'subItem' => 'email',
						'useBadges' => false
					]);
				}
			],
			'surname',
			'name',
			'patronymic',
			[
				'attribute' => 'create_date',
				'filterType' => DateTimePicker::class,
				'filterWidgetOptions' => [
					'type' => DateTimePicker::TYPE_INPUT,
					'pluginOptions' => [
						'alwaysShowCalendars' => true
					]
				]
			],
			[
				'attribute' => 'update_date',
				'filterType' => DateTimePicker::class,
				'filterWidgetOptions' => [
					'type' => DateTimePicker::TYPE_INPUT,
					'pluginOptions' => [
						'alwaysShowCalendars' => true
					]
				]
			],
			[
				'attribute' => 'store',
				'format' => 'raw',
				'value' => static function(ManagersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->stores,
						'subItem' => 'name',
						'urlScheme' => [StoresController::to('view'), 'id' => 'id'],
						'options' => static function($mapAttributeValue, $model):array {
							$url = StoresController::to('view', ['id' => $model->id]);
							return [
								'onclick' => new JsExpression("AjaxModal('$url', '{$model->formName()}-modal-view-{$model->id}');event.preventDefault();")
							];
						}
					]);
				}
			],
			[
				'attribute' => 'dealer',
				'format' => 'raw',
				'value' => static function(ManagersSearch $model):string {
					return BadgeWidget::widget([
						'items' => $model->dealers,
						'subItem' => 'name',
						'urlScheme' => [DealersController::to('view'), 'id' => 'id'],
						'options' => static function($mapAttributeValue, $model):array {
							$url = DealersController::to('view', ['id' => $model->id]);
							return [
								'onclick' => new JsExpression("AjaxModal('$url', '{$model->formName()}-modal-view-{$model->id}');event.preventDefault();")
							];
						}
					]);
				}
			],
			'deleted:boolean'
		]
	])
]) ?>