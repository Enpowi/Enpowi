<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;
use Enpowi\Modules\DataIn;
use Enpowi\Types;

$page = Types::Pages_Page((new DataIn())->in('page'));

$page->name = App::param('name');

if (empty($page->name)) {
    echo 0;
    die;
}

$page->replace(App::param('content'));

echo 1;