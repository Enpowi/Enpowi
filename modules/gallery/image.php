<?php
use Enpowi\App;
use Enpowi\Files\File;
use Enpowi\Modules\Module;
Module::is();

$file = File::getFromHash(App::param('image'));
if ($file !== null && $file->inShare()) {
	echo $file->toString();
}