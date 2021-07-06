<?php
declare(strict_types = 1);

namespace app\models\dealers;

use app\models\dealers\active_record\relations\RelDealersToStores;

/**
 *
 * Связь дилеров с магазинами
 * @property int $id
 * @property int $dealer_id
 * @property int $store_id
 */
class RelDealersToStoresSearch extends RelDealersToStores {
	public function findExistentDealerStore(int $dealerId, int $storeId):?RelDealersToStores {
		return RelDealersToStores::find()->where(['dealer_id' => $dealerId, 'store_id' => $storeId])->limit(1)->one();
	}
}
