<?php
require_once '../module.php';

use Enpowi\App;
use Enpowi\User;

$response = [];

$user = new User(
	App::param('username'),
	App::param('password')
);
$login = $user->login();

if (!$login) {
	$response['paramResponse'] = ['username'=>'Invalid username or password'];
	$response['id'] = -1;
} else {
	$response['id'] = $user->id;
}

echo json_encode($response);