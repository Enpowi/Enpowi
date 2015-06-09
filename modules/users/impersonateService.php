<?php
use Enpowi\App;
use Enpowi\Users\User;
use Enpowi\Modules\Module;
Module::is();

$user = User::fromId(App::paramInt('impersonateUserId'));

echo App::get()
	->authentication
	->impersonate($user) ? 1 : -1;