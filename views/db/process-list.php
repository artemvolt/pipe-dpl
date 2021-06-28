<?php
declare(strict_types = 1);
/**
 * @var View $this
 * @var ArrayDataProvider $dataProvider
 * @var string|false $message
 */

use pozitronik\dbmon\models\ProcessListItem;
use yii\data\ArrayDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\bootstrap4\Html;
use yii\web\View;

$this->title = 'Мониторинг процессов БД';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="panel">
	<div class="panel-content show">
		<div class="panel-content">
			<?php if ($message): ?>
				<div class="alert alert-info">
					<?= $message ?>
				</div>
			<?php endif; ?>
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'columns' => [
					'id',
					'db',
					'command',
					'time',
					'state',
					[
						'attribute' => 'user_id',
						'label' => 'user',
						'format' => 'raw'
					],
					'operation',
					'query',
					[
						'class' => ActionColumn::class,
						'template' => '{kill}',
						'buttons' => [
							'kill' => static function(string $url, ProcessListItem $model, int $key) {
								return Html::a('', ['kill', 'process_id' => $model->id], [
									'class' => 'fa fa-trash',
									'title' => 'Kill process',
									'data' => [
										'confirm' => "Kill process {$model->id}?"
									]
								]);
							}
						]
					],
				]
			]) ?>
		</div>
	</div>
</div>