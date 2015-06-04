<?php
use Enpowi\Users\User;
use Enpowi\App;
use Enpowi\Modules\Module;

Module::is();

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