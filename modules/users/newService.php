<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;
use Enpowi\Users\User;

$username = App::param('username');
$email = App::param('email');
$password = App::param('password');
$reply = [];

$stop = false;

if (!User::isValidUsername($username)) {
	$reply['username'] = 'Invalid';
}

else if (!User::isUnique($username)) {
	$reply['username'] = 'Already taken';
	$stop = true;
}

if (!User::isEmailValid($email)) {
	$reply['email'] = 'Invalid';
	$stop = true;
}

if (!User::isValidPassword($password)) {
	$reply['password'] = 'Invalid';
	$reply['repeatPassword'] = 'Invalid';
	$stop = true;
}

if ($stop) {
	echo(json_encode(['paramResponse' => $reply, 'id'=>-1]));
	die;
}

$user = User::create($username, $password, $email);

echo json_encode( [ 'id' => $user->id() ] );