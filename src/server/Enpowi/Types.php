<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/8/15
 * Time: 8:36 PM
 */

namespace Enpowi;


use Enpowi\Pages\Page;
use Enpowi\Users\Group;
use Enpowi\Users\Perm;
use Enpowi\Users\User;

class Types {

	//users
	public static function Users_User(User $user) { return $user; }
	public static function Users_Group(Group $group) { return $group; }
	public static function Users_Perm(Perm $perm) { return $perm; }

	public static function Pages_Page(Page $page) { return $page; }
}