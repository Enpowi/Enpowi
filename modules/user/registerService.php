<?php
use Enpowi\App;
use Enpowi\Users\User;
use Enpowi\Forms\Utilities;
use Enpowi\Modules\Module;

Module::is();

$email = App::param('email');
$password = App::param('password');
$repeatPassword = App::param('repeatPassword');
$captcha = App::param('captcha');
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

if (!$password === $repeatPassword) {
	$reply['password'] = 'Does not match';
	$reply['repeatPassword'] = 'Does not match';
	$stop = true;
}

else if (!User::isValidPassword($password)) {
	$reply['password'] = 'Invalid';
	$reply['repeatPassword'] = 'Invalid';
	$stop = true;
}

if ( ! Utilities::isCaptchaMatch( $captcha ) ) {
	$reply['captcha'] = 'Invalid captcha phrase';
	$stop = true;
}


if ($stop) {
	echo(json_encode(['paramResponse' => $reply, 'id'=>-1]));
	die;
}

$user = User::create($email, $password);

if ($user !== null) {
	$user->login();
	echo json_encode( [ 'id' => $user->id() ] );
	die;
} else {
	echo json_encode( [ 'id' => $user->id() ] );
	die;
}