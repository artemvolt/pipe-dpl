<?php
declare(strict_types = 1);

namespace app\modules\history\models\active_record;

use app\modules\history\HistoryModule;
use pozitronik\core\helpers\ModuleHelper;
use pozitronik\core\traits\ARExtended;
use pozitronik\helpers\ArrayHelper;
use yii\db\ActiveRecord;

/**
 * Class HistoryTags
 * Просто метки событий
 * @property int $id
 * @property int $history
 * @property string $tag
 */
class HistoryTags extends ActiveRecord {
	use ARExtended;

	public const TAG_CREATED = 'created';

	/**
	 * {@inheritDoc}
	 */
	public static function tableName():string {
		return ArrayHelper::getValue(ModuleHelper::params(HistoryModule::class), 'tableNameTags', 'sys_history_tags');
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['history', 'tag'], 'required'],
			[['tag'], 'string'],
			[['history'], 'integer'],
			[['history', 'tag'], 'unique', 'targetAttribute' => ['history', 'tag']]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'history' => 'Идентификатор операции',
			'tag' => 'Метка'
		];
	}
}