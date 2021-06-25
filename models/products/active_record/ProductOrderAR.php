<?php
declare(strict_types = 1);

namespace app\models\products\active_record;

use app\models\products\active_record\relations\RelOrderToProduct;
use app\models\products\ProductsInterface;
use app\components\db\ActiveRecordTrait;
use app\models\store\Stores;
use app\models\sys\users\Users;
use app\modules\fraud\models\behaviours\ProductOrderSimcardAsyncBehaviour;
use app\modules\status\models\traits\StatusesTrait;
use pozitronik\helpers\DateHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_order".
 *
 * @property int $id
 * @property int $initiator Заказчик
 * @property int $store Магазин
 * @property int $status Статус
 * @property string $create_date Дата регистрации
 * @property int $deleted
 *
 * @property RelOrderToProduct[] $relatedOrderToProduct Связь к промежуточной таблице к товарам заказа
 * @property ProductsInterface[] $relatedProducts Товары в заказе todo пока непонятно, как это всё будет, вернёмся к заказам позже
 *
 * @property Users $initiatorUser Пользователь, создавший заказ
 * @property Stores $storeStore Магазин поставки заказа
 */
class ProductOrderAR extends ActiveRecord {
	use StatusesTrait;
	use ActiveRecordTrait;

	/**
	 * @inheritDoc
	 */
	public function behaviors():array {
		return [
			ProductOrderSimcardAsyncBehaviour::class
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'product_order';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['initiator', 'store', 'create_date', 'status'], 'required'],
			[['initiator', 'store', 'status', 'deleted'], 'integer'],
			[['create_date'], 'default', 'value' => DateHelper::lcDate()]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'initiator' => 'Заказчик',
			'store' => 'Магазин',
			'status' => 'Статус',
			'create_date' => 'Дата регистрации',
			'deleted' => 'Deleted',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedOrderToProduct():ActiveQuery {
		return $this->hasMany(RelOrderToProduct::class, ['order_id' => 'id']);
	}

	/**s
	 * @return ActiveQuery
	 */
	public function getInitiatorUser():ActiveQuery {
		return $this->hasOne(Users::class, ['id' => 'initiator']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getStoreStore():ActiveQuery {
		return $this->hasOne(Stores::class, ['id' => 'store']);
	}

}
