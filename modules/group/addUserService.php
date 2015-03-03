<?php
require_once '../module.php';

use Enpowi\App;
use Enpowi\User;
use Enpowi\Group;

$userID = App::param('userID');
$groupName = App::param('groupName');

$user = User::fromId($userID);
$group = new Group($groupName);

$group->addUser($user);