<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Modules\DataIn;
use Enpowi\Types;

$dataIn = new DataIn();
$user = Types::Users_User($dataIn->in('user'));
$groups = $dataIn->in('groups');
$user
	->ensureExists()
	->removeAllGroups();

foreach($groups as $group) {
	Types::Users_Group($group)
		->ensureExists()
		->addUser($user);
}

echo 1;