<?php
declare(strict_types = 1);

/**
 * Шаблон главной страницы списка всех пользователей
 * @var View $this
 * @var UsersSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use app\assets\ModalHelperAsset;
use app\controllers\UsersController;
use app\models\sys\users\Users;
use app\models\sys\users\UsersSearch;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use pozitronik\grid_config\GridConfig;
use pozitronik\helpers\Utils;
use pozitronik\widgets\BadgeWidget;
use yii\data\ActiveDataProvider;
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
		'filterModel' => $searchModel,
		'panel' => [
			'heading' => $this->title.(($dataProvider->totalCount > 0)?" (".Utils::pluralForm($dataProvider->totalCount, ['пользователь', 'пользователя', 'пользователей']).")":" (нет пользователей)"),
		],
		'summary' => null !== $searchModel?Html::a('Новый пользователь', UsersController::to('create'), [
			'class' => 'btn btn-success summary-content',
			'onclick' => new JsExpression("AjaxModal('".UsersController::to('create')."', 'Users-modal-create-new');event.preventDefault();")
		]):null,
		'showOnEmpty' => true,
		'emptyText' => Html::a('Новый пользователь', UsersController::to('create'), [
			'class' => 'btn btn-success',
			'onclick' => new JsExpression("AjaxModal('".UsersController::to('create')."', 'Users-modal-create-new');event.preventDefault();")
		]),
		'toolbar' => false,
		'export' => false,
		'resizableColumns' => true,
		'responsive' => true,
		'columns' => [
			[
				'class' => ActionColumn::class,
				'template' => '{edit}{view}{update-password}{login-as-another-user}',
				'buttons' => [
					'edit' => static function(string $url, Users $model) {
						return Html::a('<i class="fas fa-edit"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$model->formName()}-modal-edit-{$model->id}');event.preventDefault();")
						]);
					},
					'view' => static function(string $url, Users $model) {
						return Html::a('<i class="fas fa-eye"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$model->formName()}-modal-view-{$model->id}');event.preventDefault();")
						]);
					},
					'update-password' => static function(string $url, Users $model) {
						return Html::a('<i class="fas fa-lock"></i>', $url, [
							'onclick' => new JsExpression("AjaxModal('$url', '{$model->formName()}-modal-update-password-{$model->id}');event.preventDefault();")
						]);
					},
					'login-as-another-user' => static function(string $url, Users $model) {/*todo: вытащить в виджет кнопку авторизации и кнопку выхода*/
						return (method_exists(Yii::$app->user, 'isLoginAsAnotherUser')
							?Html::a('<i class="fas fa-sign-in-alt"></i>', UsersController::to('login-as-another-user', ['userId' => $model->id]))
							:Html::a('<i class="fas fa-question-square"></i>', '#', ['title' => 'Не поддерживается (не сконфигурирован WebUser?)']));
					}
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
				'attribute' => 'relatedPhones',
				'format' => 'raw',
				'value' => static function(Users $user) {
					return BadgeWidget::widget([
						'items' => $user->relatedPhones,
						'subItem' => 'phone'
					]);
				}
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