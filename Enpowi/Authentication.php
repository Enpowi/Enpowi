<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/20/15
 * Time: 2:39 PM
 */

namespace Enpowi;

use Slim;

class Authentication {
	public function __construct()
	{
		session_start();
	}

	public function login($user, $password)
	{
		$user = strtolower($user);
		$user = new User($user, $password);

		if ($user->exists()) {
			$_SESSION['user'] = $user;
			return true;
		} else {
			return false;
		}

	}

	public function logout()
	{
		unset($_SESSION['user']);
	}

	public function isAuthenticated()
	{
		return isset($_SESSION['user']);
	}
}