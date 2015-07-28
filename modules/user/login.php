<?php
use Enpowi\Users\User;
use Enpowi\App;
use Enpowi\Modules\Module;

Module::is();

$response = [];

$user = User::getByEmailAndPassword(App::param('email'), App::param('password'));


if ($user === null) {
	$response['paramResponse'] = ['email'=>'Invalid email or password'];
	$response['id'] = -1;
} else {
	$login = $user->login();
	$response['id'] = $user->id;
    if (App::paramIs('remember')) {
        App::get()->authentication->rememberUserId();
    }
}

echo json_encode($response);