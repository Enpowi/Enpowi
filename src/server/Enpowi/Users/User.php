<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/20/15
 * Time: 2:39 PM
 */

namespace Enpowi\Users;

use R;
use Respect\Validation\Validator as v;
use Enpowi\Authentication;

class User {

	public $username;
	public $email;
	public $lastLogin;
	public $created;
	public $locked;
	public $groups = [];

	private $_emailPassword;
	private $_bean;

	public function __construct($username = null, $bean = null) {
		$this->username = $username;

		if ($bean === null) {
			$this->_bean = $bean = R::findOne('user', ' username = ? ', [strtolower($this->username)]);
		} else {
			$this->_bean = $bean;
		}

		if ($bean !== null) {
			$this->convertFromBean();
		}

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

	public static function create($username, $password, $email = '', $valid = false)
	{
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$numOfUsersWithThisName = R::count('user', ' username = ? ', [$username]);

		if ($numOfUsersWithThisName < 1) {
			$bean = R::dispense('user');

			$bean->username = strtolower($username);
			$bean->password = $passwordHash;
			$bean->email = $email;
			$bean->emailPassword = '';
			$bean->valid = $valid;
			$bean->locked = false;
			$bean->lastLogin = R::isoDateTime();
			$bean->created = R::isoDateTime();
			$bean->sharedGroupList;

			$id = R::store($bean);

			return new User($username, $bean);
		}

		return null;
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
		$groups = [];

		//anonymous
		if ($this->username === 'Anonymous') {
			$groups[] = new Group( 'Anonymous' );
		}

		//not anonymous
		else {
			$groupBeans = $this->_bean->sharedGroupList;

			$groups[] = new Group( 'Registered' );

			foreach($groupBeans as $groupBean) {
				$group = new Group($groupBean->name, $groupBean);
				$groups[] = $group;
			}
		}

		//everyone
		$groups[] = new Group( 'Everyone' );

		return $this->groups = $groups;
	}

	public static function users()
	{
		//TODO: paging

		$beans = R::findAll('user', ' order by username ');
		$users = [];

		foreach($beans as $bean) {
			$users[] = new User($bean->username, $bean);
		}

		return $users;
	}

	public function hasPerm($module, $component)
	{
		foreach($this->groups as $group) {
			foreach($group->perms as $perm) {
				if ($perm->module === $module || $perm->module === '*') {
					if ($perm->component === $component || $perm->component === '*') {
						return true;
					}
				}
			}
		}

		return false;
	}

	public function isSuper()
	{
		foreach ($this->groups as $group) {
			if (R::count('perm', ' group_name = ? AND module = "*" AND component = "*"', [$group->name])) {
				return true;
			}
		}

		return false;
	}
}