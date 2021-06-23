<?php
declare(strict_types = 1);

namespace app\definitions;

/**
 * @SWG\Definition(required={"restore_code", "salt"})
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="username", type="string")
 * @SWG\Property(property="login", type="string")
 * @SWG\Property(property="password", type="string")
 * @SWG\Property(property="restore_code", type="string")
 * @SWG\Property(property="salt", type="string")
 * @SWG\Property(property="is_pwd_outdated", type="boolean")
 * @SWG\Property(property="email", type="string")
 * @SWG\Property(property="comment", type="string")
 * @SWG\Property(property="create_date", type="string")
 * @SWG\Property(property="daddy", type="integer")
 * @SWG\Property(property="deleted", type="boolean")
 */
class Users {
}