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
	public function __construct()
	{
		$app = App::get();

		$this->segment = $app->session->newSegment(__CLASS__);
	}

	public function getUser()
	{
		if (isset($this->segment->user)) {
			return User::fromId($this->segment->user);
		}

		return null;
	}

	public function login($user, $password)
	{
		$user = strtolower($user);
		$user = new User($user, $password);

		if ($user->exists()) {
			$this->segment->user = $user->bean->getID();
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