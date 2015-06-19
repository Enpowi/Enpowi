<?php
use Enpowi\App;
use Enpowi\Modules\Module;
Module::is();

$files = App::paramFiles('files', 'Enpowi\\Files\\Image');

foreach ($files as $i => $file) {
	if (!$file->save()) {
		echo -1;
		die;
	}
}

echo 1;