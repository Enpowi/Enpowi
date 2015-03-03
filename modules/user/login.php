<?php
require_once '../module.php';

use Enpowi\App;
use Enpowi\User;

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