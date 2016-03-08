<?php
define('testing', true);
require_once '../modules/index.php';
use Testify\Testify;
use Enpowi\App;
use RedBeanPHP\R;

$tf = new Testify(App::$config->siteName . ' Test Suite');

$tf->beforeEach(function(){
  R::nuke();
});

$di = new RecursiveDirectoryIterator('server', RecursiveDirectoryIterator::SKIP_DOTS);
foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
  require $filename;
}

$tf();