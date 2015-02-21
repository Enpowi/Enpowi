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

	public $username;
	public $email;
	public $emailPassword;

	public $bean;

	public function __construct($username = null, $password = null) {
		if ($username !== null) {
			$this->username = $username;
			$passwordHash = password_hash($password, PASSWORD_DEFAULT);
			$this->bean = R::findOne('user', ' username = ? and password = ?', [$username, $passwordHash]);
		}
	}

	public static function fromId($id) {
		$user = new self();
		$user->bean = R::findOne('user', ' id = ? ', [$id]);
		return $user;
	}


	public function exists()
	{
		if (count($this->bean) < 1) {
			return false;
		} else {
			return true;
		}
	}

	public function create($email, $password)
	{
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$numOfUsersWithThisName = R::count('user', ' username = ? ', [$this->username]);

		if ($numOfUsersWithThisName < 1) {
			$user = R::dispense('user');
			$user->username = $this->username;
			$user->email = $email;
			$user->password = $passwordHash;
			$user->emailPassword = '';

			$id = R::store($user);
			(new Authentication())->login($this->username, $password);

			return $id;
		}

		return -1;
	}
}