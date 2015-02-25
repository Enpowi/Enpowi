<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/20/15
 * Time: 2:39 PM
 */

namespace Enpowi;

use Slim;

class Authentication
{
	public $segment;
	public function __construct($app = null)
	{
		if ($app === null) {
			$app = App::get();
		}

		$this->segment = $app->session->newSegment(__CLASS__);
	}

	public function getUser()
	{
		if (isset($this->segment->user)) {
			return User::fromId($this->segment->user);
		}

		return null;
	}

	public function login(User $user)
	{
		if (!isset($this->segment->user) && $user->exists()) {
			$this->segment->user = $user->id;
			return true;
		} else {
			return false;
		}

	}

	public function logout()
	{
		$this->segment->clear();
	}

	public function isAuthenticated()
	{
		return isset($this->segment->user);
	}
}