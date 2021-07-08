<?php
declare(strict_types = 1);

namespace app\models\sales\active_record;

use app\components\db\ActiveRecordTrait;
use app\models\core\prototypes\RelationValidator;
use app\models\products\ProductsInterface;
use app\models\sys\users\Users;
use app\modules\history\behaviors\HistoryBehavior;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sales".
 *
 * @property int $id
 * @property int $product_id id товара
 * @property int $product_type id типа товара
 * @property int $seller Продавец
 * @property string $create_date Дата регистрации
 * @property int $status Статус
 * @property int $deleted
 *
 * @property Users $relatedSeller Связанный продавец
 */
class SalesAR extends ActiveRecord {
	use ActiveRecordTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'sales';
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors():array {
		return [
			'history' => [
				'class' => HistoryBehavior::class
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['product_id', 'product_type', 'seller'], 'required'],
			[['product_id', 'product_type', 'seller', 'status', 'deleted'], 'integer'],
			[['create_date'], 'safe'],
			[['product_id', 'product_type'], 'unique', 'targetAttribute' => ['product_id', 'product_type']],
			[['relatedSeller'], RelationValidator::class],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'product_id' => 'Id товара',
			'product_type' => 'Тип товара',
			'seller' => 'Продавец',
			'create_date' => 'Дата регистрации',
			'status' => 'Статус',
			'deleted' => 'Deleted',
		];
	}

	/**
	 * @param ProductsInterface $product
	 * @return $this
	 */
	public static function findForProduct(ProductsInterface $product):self {
		if (null === $sale = self::find()->where(['product_id' => $product->id, 'product_type' => $product->type])->one()) {
			$sale = new self(['product_id' => $product->id, 'product_type' => $product->type]);
		}
		return $sale;
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedSeller():ActiveQuery {
		return $this->hasOne(Users::class, ['id' => 'seller']);
	}

	/**
	 * @param mixed $seller
	 */
	public function setRelatedSeller($seller):void {
		if (null === $seller = self::ensureModel(Users::class, $seller)) {
			throw new InvalidArgumentException("Невозможно обнаружить соответствующую модель");
		}
		/** @var Users $seller */
		$this->seller = $seller->id;
	}
}
