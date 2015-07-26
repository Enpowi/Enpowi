<?php
use Enpowi\App;
use Enpowi\Modules\DataIn;
use Enpowi\Types;
use Enpowi\Modules\Module;

Module::is();

$post = Types::Blog_Post((new DataIn())
    ->in('post'));

$post->bean();

$user = App::user();

if ($user->hasPerm('*', '*') || $post->user()->email === $user->email) {
	$post->replace( App::param( 'content' ) );
	echo 1;
} else  {
	echo -1;
}