<?php
use Enpowi\App;
use Enpowi\Users\User;
use Enpowi\Modules\Module;

Module::is();

switch (App::param('action')) {
	case 'delete':
		foreach(App::param('usernames') as $username) {
			(new User($username))->remove();
		}
		echo 1;
		break;
	case 'impersonate':
		$user = new User(App::param('impersonateUser'));
		echo App::get()
			->authentication
			->impersonate($user) ? 1 : -1;
		break;
	default:
		echo 0;
}