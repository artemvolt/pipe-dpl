<?php
declare(strict_types = 1);

namespace app\schema;


use app\schema\mutations\UsersMutationType;

/**
 * Class Types
 * @package app\schema
 */
class Types {
	private static $_tokens;
	private static $_query;
	private static $_users;

	private static $_mutation;
	private static $_usersMutation;

	/**
	 * @return QueryType
	 */
	public static function query():QueryType {
		return self::$_query?:(self::$_query = new QueryType());
	}

	/**
	 * @return UsersType
	 */
	public static function users():UsersType {
		return self::$_users?:(self::$_users = new UsersType());
	}

	/**
	 * @return TokensType
	 */
	public static function tokens():TokensType {
		return self::$_tokens?:(self::$_tokens = new TokensType());
	}

	/**
	 * @return MutationType
	 */
	public static function mutation():MutationType {
		return self::$_mutation?:(self::$_mutation = new MutationType());
	}

	/**
	 * @return UsersMutationType
	 */
	public static function usersMutation():UsersMutationType {
		return self::$_usersMutation?:(self::$_usersMutation = new UsersMutationType());
	}

}