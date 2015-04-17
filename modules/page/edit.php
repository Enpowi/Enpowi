<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Pages\Page;

$name = App::param('name');

$id = (new DataOut())
    ->add('name', $name)
	->add('page', new Page($name))
	->bind();
?>
<form
	class="container"
	v-module
	data="<?php echo $id?>"
	action="page/editService"
    data-done="page?name={{ name }}">

	<h3>
        <span v-t>Editing Page: </span>
        {{ page.name }}
        <input type="{{ page.name === null ? 'text' : 'hidden' }}" v-hide=" page.name === null " name="name" value="{{ name }}" v-model="name" />
    </h3>

	<input type="hidden" name="page" value="{{ stringify(page) }}">

	<textarea name="content" class="wide">{{ page.content }}</textarea>

	<button type="submit" class="btn btn-success" v-t>Submit</button>
</form>
<script src="modules/page/edit.js"></script>