<?php
declare(strict_types = 1);

namespace app\definitions;

/**
 * @SWG\Swagger(
 *     basePath="/",
 *     produces={"application/json"},
 *     consumes={"application/x-www-form-urlencoded"},
 *     @SWG\Info(version="1.0", title="Simple API"),
 * )
 * @SWG\Get(path="/api/users",
 *     tags={"Users"},
 *     summary="Retrieves the collection of Users resources.",
 *     @SWG\Response(
 *         response = 200,
 *         description = "Users collection response",
 *         @SWG\Schema(ref = "#/definitions/Users")
 *     ),
 * )
 */
class UsersController {

}