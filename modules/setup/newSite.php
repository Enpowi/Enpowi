<?php

$dir = dirname(__FILE__);

define('path', dirname(dirname($dir)));

if (file_exists($dir . '/config.lock')) {
	throw new Exception('locked');
}

if (!is_writable($dir)) {
	//throw new Exception('modules/setup folder needs write permission');
}

if (!file_exists($dir . '/config.php')) {
	throw new Exception('You must first create modules/setup/config.php');
}

require_once $dir . '/../../vendor/autoload.php';
require_once $dir . '/../setup/config.php';

use RedBeanPHP\R;
use Enpowi\Users\Group;
use Enpowi\Users\User;
use Enpowi\Users\Perm;
use Enpowi\App;
use Enpowi\Modules\Module;

Module::run();
R::nuke();

App::log('setup', 'newSite');

//create groups
$everyoneGroup = Group::create('Everyone', false, false, true);
$anonymousGroup = Group::create('Anonymous', false, true);
$registeredGroup = Group::create('Registered', true);
$administratorGroup = Group::create('Administrator', false, false, false, true);

//create first user & put him in admin group
$administratorUser = User::create('admin', 'admin', '', true);
$administratorGroup->addUser($administratorUser);

//give Anonymous abilities
Perm::create('user', 'login', $anonymousGroup);
Perm::create('user', 'register', $anonymousGroup);

//give registered abilities
Perm::create('user', 'view', $registeredGroup);
Perm::create('user', 'logout', $registeredGroup);
Perm::create('user', 'confirm', $registeredGroup);
Perm::create('page', 'edit', $registeredGroup);

//give Administrator group access to everything
Perm::create('*', '*', $administratorGroup);

//give everyone the ability to see modules with no security
Perm::create('app', '*', $everyoneGroup);
Perm::create('default', '*', $everyoneGroup);
Perm::create('setup', '*', $everyoneGroup);
Perm::create('page', 'index', $everyoneGroup);

if (is_writable($dir)) {
    echo 'cannot write lock file. ';
} else {
    file_put_contents($dir . '/config.lock', '');
}

echo 'setup complete';