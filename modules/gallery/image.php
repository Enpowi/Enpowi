<?php
use Enpowi\App;
use Enpowi\Files\Image;
use Enpowi\Modules\Module;
Module::is();

$image = \Enpowi\Types::Files_Image(Image::getFromHash(App::param('image')));
if ($image !== null && $image->inShare()) {
	if (App::paramIs('thumb')) {
		echo $image->toThumbString();
	} else {
		echo $image->toString();
	}
}