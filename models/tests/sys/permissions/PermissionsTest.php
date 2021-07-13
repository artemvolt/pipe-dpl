<?php
declare(strict_types = 1);

namespace app\models\tests\sys\permissions;

use app\models\sys\permissions\Permissions;
use app\models\tests\core\prototypes\ActiveRecordTraitTest;

/**
 * Class PermissionsTest
 * @package app\models\tests\sys\permissions
 */
class PermissionsTest extends Permissions {
	use ActiveRecordTraitTest;

	/**
	 * @param string $controller
	 * @param string $action
	 * @param string|null $verb
	 * @return PermissionsTest
	 */
	public static function create(string $controller, string $action, ?string $verb = null):PermissionsTest {
		$self = new self();

		$self->name = "{$controller}_{$action}";
		if ($verb) {
			$verbLow = strtolower($verb);
			$self->name .= "_".$verbLow;
		}
		$self->controller = $controller;
		$self->action = $action;
		$self->verb = $verb;
		return $self;
	}

	/**
	 * @return PermissionsTest
	 */
	public static function loginAsAnotherUser():self {
		$self = new self();
		$self->name = 'login_as_another_user';
		$self->controller = 'users';
		$self->action = 'login-as-another-user';
		$self->verb = 'GET';
		return $self;
	}

	/**
	 * @return PermissionsTest
	 */
	public static function loginBack():self {
		$self = new self();
		$self->name = 'login_back';
		$self->controller = 'users';
		$self->action = 'login-back';
		$self->verb = 'GET';
		return $self;
	}

	/**
	 * @return static
	 */
	public static function apiAuthToken():self {
		$self = new self();
		$self->name = 'api_auth_token';
		$self->controller = 'auth';
		$self->action = 'token';
		$self->verb = 'POST';
		return $self;
	}

	/**
	 * @return static
	 */
	public static function apiVersionIndex():self {
		$self = new self();
		$self->name = 'api_version_index';
		$self->controller = 'version';
		$self->action = 'index';
		$self->verb = 'POST';
		return $self;
	}

	/**
	 * @param string $action
	 * @return PermissionsTest
	 */
	public static function sellersInviteLinksCreate(string $action):PermissionsTest {
		$self = new self();
		$self->name = "sellers_invite_links_create_{$action}";
		$self->controller = 'sellers-invite-links';
		$self->action = 'create';
		$self->verb = $action;
		return $self;
	}

	/**
	 * @param string $action
	 * @return PermissionsTest
	 */
	public static function sellersInviteLinksEdit(string $action):PermissionsTest {
		$self = new self();
		$self->name = "sellers_invite_links_edit_{$action}";
		$self->controller = 'sellers-invite-links';
		$self->action = 'edit';
		$self->verb = $action;
		return $self;
	}

	/**
	 * @return PermissionsTest
	 */
	public static function sellersInviteLinksIndex():PermissionsTest {
		$self = new self();
		$self->name = 'sellers_invite_links_index';
		$self->controller = 'sellers-invite-links';
		$self->action = 'index';
		$self->verb = 'GET';
		return $self;
	}

	public static function sellersAssignMiniWithStore(string $verb):self {
		$self = new self();
		$self->name = "sellers-assign-mini-with-store-{$verb}";
		$self->controller = 'sellers';
		$self->action = 'assign-mini-with-store';
		$self->verb = $verb;
		return $self;
	}

	/**
	 * @return PermissionsTest
	 */
	public static function sellersIndex():PermissionsTest {
		$self = new self();
		$self->name = "sellers-index";
		$self->controller = 'sellers';
		$self->action = 'index';
		$self->verb = 'GET';
		return $self;
	}
}