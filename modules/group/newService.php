<?php
require_once '../module.php';

use Enpowi\App;
use Enpowi\Group;

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