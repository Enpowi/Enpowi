<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Pages\Page;

$id = (new DataOut())
	->add('page', new Page(App::param('name')))
	->bind();
?>
<form
	class="container"
	v-module
	data="<?php echo $id?>"
	action="page/editService">

	<h3><span v-t>Editing Page: </span>{{ page.name }}</h3>

	<input type="hidden" name="page" value="{{ stringify(page) }}">

	<textarea name="content" class="wide">{{ page.content }}</textarea>

	<button type="submit" class="btn btn-success" v-t>Submit</button>
</form>
<script src="modules/page/edit.js"></script>