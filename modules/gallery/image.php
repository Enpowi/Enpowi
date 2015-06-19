<?php
use Enpowi\App;
use Enpowi\Files\File;
use Enpowi\Modules\Module;
Module::is();

$file = File::getFromHash(App::param('image'));
if ($file !== null && $file->username === App::user()->username) {
	echo $file->toString();
}