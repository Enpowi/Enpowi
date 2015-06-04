<?php
use Enpowi\Modules\DataIn;
use Enpowi\Types;
use Enpowi\Modules\Module;

Module::is();

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