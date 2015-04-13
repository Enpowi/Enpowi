<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/20/15
 * Time: 2:39 PM
 */

namespace Enpowi;

use Slim;
use Enpowi\Users\User;

class Authentication
{
	public $segment;
	public function __construct($app = null)
	{
		if ($app === null) {
			$app = App::get();
		}

		$this->segment = $app->session->getSegment(__CLASS__);
	}

	public function getUser()
	{
		if (($_user = $this->segment->get('user')) !== null) {
			$user = User::fromId($_user);
			if ($user === null) {
				$this->logout();
			} else {
				return $user;
			}
		}

		return new User('Anonymous');
	}

	public function login(User $user)
	{
		if ($user->exists()) {
			$this->segment->set('user', $user->id());
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
		return $this->segment->get('user') !== null;
	}
}