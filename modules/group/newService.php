<?php
use Enpowi\App;
use Enpowi\Users\Group;
use Enpowi\Modules\Module;

Module::is();

$groupName = App::param('groupName');
$reply = [];

$stop = false;

if (!Group::isValidGroupName($groupName)) {
	$reply['groupName'] = 'Invalid';
}

if ($stop) {
	echo(json_encode(['paramResponse' => $reply, 'id'=>-1]));
	die;
}

$id = Group::create($groupName);

echo json_encode(['id'=>$id]);