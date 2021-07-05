<?php
declare(strict_types = 1);

namespace app\models\seller\active_record;

use app\models\store\Stores;

/**
 * This is the model class for table "sellers_invite_links".
 *
 * @property int $id
 * @property int $store_id
 * @property int $phone_number
 * @property string $email
 * @property string $expired_at
 *
 * @property Stores $store
 */
class SellerInviteLink extends SellerInviteLinkAr {
}
