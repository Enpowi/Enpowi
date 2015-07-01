<?php
use Enpowi\App;
use Enpowi\Users\User;
use Enpowi\Modules\Module;

Module::is();

$email = App::param('email');
$password = App::param('password');
$reply = [];

$stop = false;

if (!User::isEmailValid($email)) {
	$reply['email'] = 'Invalid';
	$stop = true;
}

if (!User::isUnique($email)) {
    $reply['email'] = 'Already taken';
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

$user = User::create($email, $password);

echo json_encode( [ 'id' => $user->id ] );