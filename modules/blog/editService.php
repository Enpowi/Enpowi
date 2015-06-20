<?php
use Enpowi\App;
use Enpowi\Modules\DataIn;
use Enpowi\Types;
use Enpowi\Modules\Module;

Module::is();

$post = Types::Blog_Post((new DataIn())
    ->in('post'));

$post->bean();
$post->replace(App::param('content'));

echo 1;