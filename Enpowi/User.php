<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/20/15
 * Time: 2:39 PM
 */

namespace Enpowi;

use R;

class User {

	public $name;
	public $email;
	public $emailPassword;

	public $bean;

	public function __construct($name, $password) {
		$this->name = $name;
		$this->bean = R::findOne('user', ' name = ? and password = ?', [$name, $password]);
	}

	public function exists()
	{
		if (count($this->bean) < 1) {
			return false;
		} else {
			return true;
		}
	}

	public function create($password)
	{
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$numOfUsersWithThisName = R::count('user', ' name = ? ', [$this->name]);

		if ($numOfUsersWithThisName < 1) {
			$user = R::dispense('user');
			$user->name = $this->name;
			$user->pass = $passwordHash;

			$id = R::store($user);
			(new Authentication())->login($user, $passwordHash);

			return $id;
		}

		return -1;
	}
}