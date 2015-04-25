<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;
use Enpowi\Modules\DataIn;
use Enpowi\Types;

$post = Types::Blog_Post((new DataIn())
    ->in('post'));

$post->replace(App::param('content'));

echo 1;