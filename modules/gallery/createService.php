<?php
use Enpowi\App;
use Enpowi\Modules\Module;
use Enpowi\Files\Gallery;

Module::is();

$name = App::param('name');
$description = App::param('description');

if (!Gallery::isUnique($name)) {
	Module::paramRespond('name', 'Not unique');
} else {
	$id = Gallery::create( $name, $description );
	Module::successRespond('g', $id);
}