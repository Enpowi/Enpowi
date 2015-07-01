<?php
use Enpowi\App;
use Enpowi\Modules\Module;
use Enpowi\Files\Gallery;
use Enpowi\Types;

Module::is();

$galleryId = App::paramInt('g');
$gallery = new Gallery($galleryId);

if ($gallery->exists()) {
	$images = App::paramFiles( 'files', 'Enpowi\\Files\\Image' );

	foreach ( $images as $i => $_image ) {
		$image = Types::Files_Image($_image);
		if ($image->upload()) {
			$gallery->addImage($image);
		} else {
			echo - 1;
			die;
		}
	}

	$gallery->save();

	echo 1;
} else {
	echo -1;
}