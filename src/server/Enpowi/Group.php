<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2/26/15
 * Time: 5:13 PM
 */

namespace Enpowi;

use R;
use Respect\Validation\Validator as v;

class Group {

	public $name;
	public $bean;

	public function __construct($name, $bean = null)
	{
		$this->name = $name;

		if ($bean === null) {
			$this->bean = R::find( 'group', ' name = ? ', [ $name ] );
		} else {
			$this->bean = $bean;
		}
	}

	public static function create($groupName)
	{
		$count = R::count( 'group', ' name = ? ', [ $groupName ] );

		if ($count < 1) {
			$bean = R::dispense('group');
			$bean->name = $groupName;
			$bean->ownUserList;

			return R::store($bean);
		}

		return -1;
	}

	public function remove()
	{
		$bean = R::find( 'group', ' name = ? ', [ $this->name ] );

		if ($bean !== null) {
			R::trash($bean);
			return true;
		}
		return false;
	}

	public function addUser(User $user)
	{
		$userBean = $user->bean;
		$groupBean = R::find( 'group', ' name = ? ', [ $this->name ] );

		if ($groupBean !== null && $user !== null) {
			$groupBean->ownUserList[] = $userBean;
			$userBean->ownGroupList[] = $groupBean;

			R::storeAll([$groupBean, $userBean]);
			return false;
		}
		return true;
	}

	public function removeUser(User $user)
	{
		$userBean = $user->bean;
		$groupBean = R::find( 'group', ' name = ? ', [ $this->name ] );

		if ($groupBean !== null && $user !== null) {
			unset($groupBean->ownUserList[$userBean->getID()]);
			unset($userBean->ownGroupList[$groupBean->getID()]);
			R::storeAll([$groupBean, $userBean]);
			return true;
		}

		return false;
	}

	public function countUsers()
	{
		return R::count( 'group', ' name = ? ', [ $this->name ] );
	}

	public function users()
	{
		$groupBean = R::find( 'group', ' name = ? ', [ $this->name ] );
		$users = [];
		foreach($groupBean->ownUserList as $userBean) {
			$users[] = new User($userBean->username, $userBean);
		}
		return $users;
	}

	public static function groups()
	{
		$groups = [];

		foreach(R::find('group') as $groupBean) {
			$groups[] = new Group($groupBean->name, $groupBean);
		}

		return $groups;
	}

	public static function isValidGroupName($groupName)
	{
		return v::alnum()
			->noWhitespace()
			->length(3,200)
			->validate($groupName);
	}
}