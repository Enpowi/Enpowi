<?php
define('testing', true);
require_once '../modules/index.php';
use Testify\Testify;
use Enpowi\App;

$tf = new Testify(App::$config->siteName . ' Test Suite');

$di = new RecursiveDirectoryIterator('server', RecursiveDirectoryIterator::SKIP_DOTS);
foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
  try {
    require $filename;
  } catch (Exception $e) {

  }
}

$tf();