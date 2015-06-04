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
	break;
}


echo 1;