<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;
use Enpowi\Users\User;

switch (App::param('action')) {
	case 'delete':
		foreach(App::param('usernames') as $username) {
			(new User($username))->remove();
		}
	break;
}


echo 1;