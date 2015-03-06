<?php
require_once 'setup/run.php';

use Enpowi\App;
error_reporting(E_ALL);
ini_set("display_errors", 1);

define('Modular', TRUE);

$component = App::loadComponent(dirname(__file__), App::param('module'), App::param('component'));
if ($component !== null && !empty($component->file)) {
	require_once $component->file;
} else {
	echo -1;
}
//TODO: ssl