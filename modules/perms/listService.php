<?php
use Enpowi\Modules\Module;

Module::is();

$perms = [];
$groupNames = Enpowi\App::param('groupNames');

foreach($groupNames as $groupName) {
	(new \Enpowi\Users\Group($groupName))->removePerms();
}

foreach(Enpowi\App::param('perm') as $permUnparsed) {
	$parsed = Enpowi\Users\Perm::parse($permUnparsed);
	$group = new \Enpowi\Users\Group($parsed['group']);
	\Enpowi\Users\Perm::create($parsed['module'], $parsed['component'], $group);
}

Module::successRespond('success', true);