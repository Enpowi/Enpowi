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
	public $lastLogin;
	public $created;
	public $locked;
	public $groupList = [];

	private $_emailPassword;
	private $_bean;

	public function __construct($username, $bean = null) {
		$this->username = $username;

		if ($bean === null) {
			$this->_bean = R::findOne('user', ' username = ? ', [strtolower($this->username)]);
		} else {
			$this->_bean = $bean;
		}

		$this->convertFromBean();
		$this->updateGroups();
	}

	public static function getByUsernameAndPassword($username, $password)
	{
		$user = new User($username);
		$bean = $user->_bean;

		if ($bean !== null) {
			if (password_verify($password, $bean->password)) {
				return $user;
			}
		}

		return null;
	}

	private function convertFromBean()
	{
		$bean = $this->_bean;

		if (!$this->exists()) return;

		$this->username = $bean->username;
		$this->email = $bean->email;
		$this->_emailPassword = $bean->emailPassword;
		$this->lastLogin = $bean->lastLogin;
		$this->created = $bean->created;
		$this->locked = $bean->locked;
	}

	public static function fromId($id) {
		$bean = R::findOne('user', ' id = ? ', [$id]);

		if ($bean === null) return null;

		$user = new User($bean->username, $bean);

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
		$result = R::count('user', ' username = ?', [strtolower($username)]);
		return $result === 0;
	}

	public function exists()
	{
		if ($this->_bean === null) {
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

			$bean->username = strtolower($this->username);
			$bean->password = $passwordHash;
			$bean->email = $email;
			$bean->emailPassword = '';
			$bean->valid = $valid;
			$bean->locked = false;
			$bean->lastLogin = R::isoDateTime();
			$bean->created = R::isoDateTime();
			$bean->sharedGroupList;

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

	public function bean()
	{
		return $this->_bean;
	}

	public function id()
	{
		return $this->_bean->getID();
	}

	public function updateGroups()
	{
		$groupBeans = $this->_bean->ownGroupList;
		$groups = [];

		foreach($groupBeans as $groupBean) {
			$group = new Group($groupBean->name, $groupBean);
			$groups[] = $group;
		}

		return $this->groupList = $groups;
	}

	public static function users()
	{
		$beans = R::findAll('user', ' order by username ');
		$users = [];

		foreach($beans as $bean) {
			$users[] = new User($bean->username, $bean);
		}

		return $users;
	}
}