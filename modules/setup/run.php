<?php

$thisDir = dirname(__FILE__);

define('path', dirname(dirname($thisDir)));

require_once path . '/vendor/autoload.php';

if (defined('testing')) {
	if (file_exists(path . '/test/config.php')) {
		require_once path . '/test/config.php';
	} else {
		throw new Exception('No testing config found at: ./test/config.php perhaps copy ./modules/setup/config.default.php there?');
	}
} else {
	if (file_exists($thisDir . '/config.php')) {
		require_once $thisDir . '/config.php';
	} else {
		throw new Exception('No config found at: ./modules/setup/config.php');
	}
}