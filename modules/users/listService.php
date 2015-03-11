<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;
use Enpowi\Users\User;

foreach(App::param('usernames') as $username) {
	(new User($username))->remove();
}

echo 1;