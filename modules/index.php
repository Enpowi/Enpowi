<?php
require_once 'setup/run.php';

use Enpowi\App;
use Enpowi\Modules\Module;

Module::run();

error_reporting(E_ALL);
ini_set("display_errors", 1);

$module = App::param('module');
$component = App::param('component');
$path = dirname(__FILE__);
$me = App::loadComponent($path, $module, $component);

if ($me !== null && !empty($me->file)) {
	require_once $me->file;
} else {
	echo -1;
}
//TODO: ssl