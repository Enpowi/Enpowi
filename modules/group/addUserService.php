<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;
use Enpowi\Users\User;
use Enpowi\Users\Group;

$userID = App::param('userID');
$groupName = App::param('groupName');

$user = User::fromId($userID);
$group = new Group($groupName);

$group->addUser($user);