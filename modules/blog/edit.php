<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Blog\Post;

$name = App::param('name');

$data = (new DataOut())
    ->add('editing', empty($name))
	->add('post', new Post($name))
	->out();
?>

<form
	class="container"
	v-module
    data="<?php echo $data?>"
	action="blog/editService"
    data-done="blog?name={{ post.name }}">

	<h3>
        <span v-t>Editing:</span>
        <span v-show=" !editing ">{{ post.name }}</span><br>
    </h3>

    <span v-show=" editing " class="wide fixed">
        <input class="form-control" type="text" name="name" v-model="post.name" v-placeholder="Post Name" />
    </span>

    <div class="form-group">
        <input type="hidden" name="post" value="{{ stringify(post) }}">

        <div class="form-control height-auto">
            <textarea
                v-source-edit="wikilingo"
                name="content"
                class="wide">{{ post.content }}</textarea>
        </div>
    </div>

	<button type="submit" class="btn btn-success" v-t>Submit</button>
</form>