<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/20/15
 * Time: 2:39 PM
 */

namespace Enpowi;

use R;
use Respect\Validation\Validator as v;

class User {

	public $username;
	public $email;
	public $id;
	public $lastLogin;
	public $created;
	public $locked;

	private $_emailPassword;
	private $bean;

	public function __construct($username = null, $password = null) {
		if ($username !== null) {
			$username = strtolower($username);
			$this->username = $username;
			$bean = R::findOne('user', ' username = ? and locked = ? ', [$username, false]);
			if ($bean !== null) {
				if (password_verify($password, $bean->password)) {
					$this->convertFromBean($bean);
				}
			}
		}
	}

	private function convertFromBean($bean)
	{
		$this->bean = $bean;
		$this->id = $bean->getID();
		$this->bean = $bean;
		$this->username = $bean->username;
		$this->email = $bean->email;
		$this->_emailPassword = $bean->emailPassword;
		$this->lastLogin = $bean->lastLogin;
		$this->created = $bean->created;
		$this->locked = $bean->locked;
	}

	public static function fromId($id) {
		$user = new self();
		$bean = R::findOne('user', ' id = ? ', [$id]);
		if ($bean !== null) {
			$user->convertFromBean($bean);
		}

		return $user;
	}

	public static function isValidUsername($username)
	{
		return v::alnum()
			->noWhitespace()
			->length(3,200)
			->validate($username);
	}

	public static function isValidPassword($password)
	{
		return v::noWhitespace()
			->length(8)
			->validate($password);
	}

	public static function isEmailValid($email)
	{
		return V::email()->validate($email);
	}

	public static function isUnique($username)
	{
		$result = R::count('user', ' username = ?', [$username]);
		return $result === 0;
	}

	public function exists()
	{
		if ($this->bean === null) {
			return false;
		} else {
			return true;
		}
	}

	public function create($email, $password, $valid = false)
	{
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$numOfUsersWithThisName = R::count('user', ' username = ? ', [$this->username]);

		if ($numOfUsersWithThisName < 1) {
			$bean = R::dispense('user');

			$bean->username = $this->username;
			$bean->password = $passwordHash;
			$bean->email = $email;
			$bean->emailPassword = '';
			$bean->valid = $valid;
			$bean->locked = false;
			$bean->lastLogin = R::isoDateTime();
			$bean->created = R::isoDateTime();

			$id = R::store($bean);
			(new Authentication())->login($this);

			return $id;
		}

		return -1;
	}

	public function login()
	{
		return (new Authentication())->login($this);
	}

	public function logout()
	{
		(new Authentication())->logout();
	}

	public function emailPassword()
	{
		return $this->_emailPassword;
	}
}