<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;
use Enpowi\Users\User;

$response = [];

$user = User::getByUsernameAndPassword(App::param('username'), App::param('password'));

if ($user === null) {
	$response['paramResponse'] = ['username'=>'Invalid username or password'];
	$response['id'] = -1;
} else {
	$login = $user->login();
	$response['id'] = $user->id();
}

echo json_encode($response);