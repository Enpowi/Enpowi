<?php
use Enpowi\App;
use Enpowi\Modules\DataIn;
use Enpowi\Types;
use Enpowi\Modules\Module;

Module::is();

switch (App::param('action')) {
    case '':
}
$page = Types::Pages_Page((new DataIn())
    ->in('page'));

$page->replace(App::param('content'));

echo 1;