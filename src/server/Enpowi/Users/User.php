<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/20/15
 * Time: 2:39 PM
 */

namespace Enpowi\Users;

use Enpowi\App;
use RedBeanPHP\R;
use Respect\Validation\Validator as v;
use Enpowi\Authentication;
use Enpowi\Event;
use Enpowi\Generic;

class User extends Generic\PageableDataItem {
	public $email;
	public $lastLogin;
	public $created;
	public $locked;
	public $valid;
    public $id;

	/**
	 * @var Group[]
	 */
	public $groups = [];

	private $_bean;

	public function __construct($email = null, $bean = null) {
		$this->email = $email;

		if ($bean === null) {
			$this->_bean = $bean = R::findOne('user', ' email = ? ', [strtolower($this->email)]);
		} else {
			$this->_bean = $bean;
		}

		if ($bean !== null) {
			$this->convertFromBean();
		}

		$this->updateGroups();
	}

	public static function getByEmailAndPassword($email, $password)
	{
		$user = new User($email);
		$bean = $user->bean();

		if ($bean !== null) {
			if (password_verify($password, $bean->password)) {
				return $user;
			}
		}

		return null;
	}

    public static function getByEmail($email)
    {
        $bean = R::findOne('user', 'email = :email', ['email' => strtolower($email)]);
        if ($bean !== null) {
            $user = new User($bean->email, $bean);
            return $user;
        }
        return null;
    }

	public function convertFromBean()
	{
		$bean = $this->bean();

		if (!$this->exists()) return $this;

		$this->email = strtolower($bean->email);
		$this->lastLogin = $bean->lastLogin;
		$this->created = $bean->created;
		$this->locked = $bean->locked;
		$this->valid = $bean->valid == 1 ? true : false;
        $this->id = $bean->id;

		return $this;
	}

	public static function fromId($id) {
		$bean = R::findOne('user', ' id = ? ', [$id]);

		if ($bean === null) return null;

		$user = new User($bean->email, $bean);

		return $user;
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

	public static function isUnique($email)
	{
		$result = R::count('user', ' email = ?', [strtolower($email)]);
		return $result === 0;
	}

	public static function create($email, $password, $valid = false)
	{
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$numOfUsersWithThisName = R::count('user', ' email = ? ', [$email]);

		if ($numOfUsersWithThisName < 1) {
			$bean = R::dispense('user');

            $bean->email = strtolower($email);
			$bean->password = $passwordHash;
			$bean->valid = $valid;
			$bean->locked = false;
			$bean->lastLogin = R::isoDateTime();
			$bean->created = R::isoDateTime();
			$bean->sharedGroupList;
			$bean->lockedKey = App::guid();
			$bean->validationKey = App::guid();
            $bean->validationDate = R::isoDateTime();

			Event\User\BeforeStore::pub($bean);

			$id = R::store($bean);

			$user = new User($email, $bean);

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

	public function bean()
	{
		if ($this->_bean === null) {
			$this->_bean = R::findOne('user', ' email = ? ', [strtolower($this->email)]);
		}

		return $this->_bean;
	}

	public function ensureExists()
	{
		if ($this->_bean === null) {
			$this->_bean = R::findOne('user', ' email = ? ', [$this->email]);
		}

		return $this;
	}

	public function lockedKey() {
		return $this->_bean->lockedKey;
	}

	public function isLocked() {
		return $this->_bean->locked ? true : false;
	}

    public function canValidate()
    {
        return (R::isoDateTime() - (60*60*24)) < $this->_bean->validationDate;
    }

	public function validationKey() {
		return $this->_bean->validationKey;
	}

	public function isValid() {
		return $this->_bean->valid == 1 ? true : false;
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
		$bean = $this->bean();

		//anonymous
		if ($bean === null) {
			$group = new Group( 'Anonymous' );
			$groups[] = $group;
		}

		//not anonymous
		else {
			$groupBeans = $bean->sharedGroupList;

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

	public static function users($pageNumber = 1)
	{
		$beans = R::findAll('user', ' order by email limit :offset, :count', [
			'offset' => App::pageOffset($pageNumber),
			'count' => App::$pagingSize
		]);

		$users = [];

		foreach($beans as $bean) {
			$users[] = new User($bean->email, $bean);
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
						|| ($perm->component . '_service') === $component
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
		$bean = R::findOne( 'user', ' email = ? ', [ $this->email ] );

		if ($bean !== null) {
			R::trash($bean);
			return true;
		}
		return false;
	}

    public function updatePassword($password)
    {
        if (self::isValidPassword($password)) {
            $bean = $this->bean();
            $bean->password = password_hash($password, PASSWORD_DEFAULT);
            R::store($bean);
            return true;
        }
        return false;
    }

	public function updateEmail($email)
	{
		if (self::isEmailValid($email)) {
			$bean = $this->bean();
			$bean->email = $email;
			R::store($bean);
			return true;
		}
		return false;
	}

    public function resetPassword()
    {
        $password = Authentication::generatePassword();
        $bean = $this->bean();
	    if ($bean !== null) {
		    $bean->password = password_hash( $password, PASSWORD_DEFAULT );
		    R::store( $bean );
		    return $password;
	    }

	    return null;
    }

	public static function pages()
	{
		return R::count('user') / App::$pagingSize;
	}
}
