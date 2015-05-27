<?php

$thisDir = dirname(__FILE__);

define('path', dirname(dirname($thisDir)));

require_once $thisDir . '/../../vendor/autoload.php';
if (file_exists($thisDir . '/config.php')) {
	require_once $thisDir . '/config.php';
}