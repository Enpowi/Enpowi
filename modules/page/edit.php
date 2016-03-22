<?php
use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Pages\Page;
use Enpowi\Modules\Module;

Module::is();

$name = App::param('name');

(new DataOut)
  ->add('editing', empty($name))
	->add('page', new Page($name))
	->bind();
?>

<form
	class="container"
	v-module
	action="page/editService"
    data-done="page?name={{ page.name }}">

	<h3>
        <span v-t>Editing:</span>
        <span v-show=" !editing ">{{ page.name }}</span><br>
    </h3>

    <span v-show=" editing " class="wide fixed">
        <input class="form-control" type="text" name="name" v-model="page.name" v-placeholder="Page Name" />
    </span>

    <div class="form-group">
        <input type="hidden" name="page" value="{{ stringify(page) }}">

        <div class="form-control height-auto">
            <textarea
                v-source-edit="wikilingo"
                name="content"
                class="wide">{{ page.content }}</textarea>
        </div>
    </div>

	<button type="submit" class="btn btn-success" v-t>Submit</button>
</form>