<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;
use Enpowi\Modules\DataIn;
use Enpowi\Types;

switch (App::param('action')) {
    case '':
}
$page = Types::Pages_Page((new DataOut())
    ->in('page'));

$page->replace(App::param('content'));

echo 1;