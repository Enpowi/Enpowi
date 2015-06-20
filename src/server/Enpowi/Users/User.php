<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/20/15
 * Time: 2:39 PM
 */

namespace Enpowi\Users;

use ENpowi\App;
use RedBeanPHP\R;
use Respect\Validation\Validator as v;
use Enpowi\Authentication;
use Enpowi\Event;

class User {

	public $username;
	public $email;
	public $lastLogin;
	public $created;
	public $locked;
	public $valid;

	/**
	 * @var Group[]
	 */
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
		$this->valid = $bean->valid;
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
			$bean->lockedKey = App::guid();
			$bean->validationKey = App::guid();

			Event\User\BeforeStore::pub($bean);

			$id = R::store($bean);

			$user = new User($username, $bean);

			Event\User\Create::pub($user);

			return $user;
		}

		return null;
	}

	public function login()
	{
		$login = (new Authentication())->login($this);

		Event\User\Login::pub($login, $this);

		return $login;
	}

	public function logout()
	{
		Event\User\Logout::pub($this);

		(new Authentication())->logout();

		return $this;
	}

	public function emailPassword()
	{
		return $this->_emailPassword;
	}

	public function bean()
	{
		if ($this->_bean === null) {
			$this->_bean = R::findOne('user', ' username = ? ', [strtolower($this->username)]);
		}

		return $this->_bean;
	}

	public function ensureExists()
	{
		if ($this->_bean === null) {
			$this->_bean = R::findOne('user', ' username = ? ', [$this->username]);
		}

		return $this;
	}

	public function id()
	{
		return $this->_bean->getID();
	}

	public function lockedKey() {
		return $this->_bean->lockedKey;
	}

	public function isLocked() {
		return $this->_bean->locked ? true : false;
	}

	public function validationKey() {
		return $this->_bean->validationKey;
	}

	public function isValid() {
		return $this->_bean->valid ? true : false;
	}

	public function setValid($valid) {
		$bean = $this->_bean;

		$bean->valid = $valid;

		R::store($bean);

		Event\User\Valid::pub($valid, $this);

		return $this;
	}

	public function updateGroups()
	{
		$groups = [];
		$group = null;

		//anonymous
		if ($this->username === 'Anonymous') {
			$group = new Group( 'Anonymous' );
			$groups[] = $group;
		}

		//not anonymous
		else {
			$groupBeans = $this->_bean->sharedGroupList;

			$group = new Group( 'Registered' );
			$groups[] = $group;

			foreach($groupBeans as $groupBean) {
				$group = new Group($groupBean->name, $groupBean);
				$groups[] = $group;
			}
		}

		//everyone
		$group = new Group( 'Everyone' );
		$groups[] = $group;

		return $this->groups = $groups;
	}

	public static function users($pageNumber = 0)
	{
		$beans = R::findAll('user', ' order by username limit :offset, :count', [
			'offset' => $pageNumber * App::$pagingSize,
			'count' => App::$pagingSize
		]);

		$users = [];

		foreach($beans as $bean) {
			$users[] = new User($bean->username, $bean);
		}

		return $users;
	}

	public function hasPerm($module, $component)
	{
		foreach($this->groups as $group) {
			$group->updatePerms();
			foreach($group->perms as $perm) {
				if (
					$perm->module === $module
					|| $perm->module === '*'
				) {
					if (
						$perm->component === $component
						|| ($perm->component . 'Service') === $component
					    || $perm->component === '*'
					) {
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


	public function removeAllGroups() {
		$this->updateGroups();
		foreach($this->groups as $group) {
			$group->removeUser($this);
		}
		return $this;
	}

	public function updatePerms() {
		$this->updateGroups();
		foreach($this->groups as $group) {
			$group->updatePerms();
		}
		return $this;
	}

	public function remove()
	{
		$bean = R::findOne( 'user', ' username = ? ', [ $this->username ] );

		if ($bean !== null) {
			R::trash($bean);
			return true;
		}
		return false;
	}
}