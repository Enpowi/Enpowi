<?php
use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Blog\Post;
use Enpowi\Modules\Module;

Module::is();

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

    <table v-show=" editing " class="wide fixed">
	    <tbody>
	    <tr>
		    <td>
			    <input
				    class="form-control"
				    type="text"
				    name="name"
				    v-model="post.name"
				    v-placeholder="Post Name">
		    </td>
		    <td>
			    <input
				    class="form-control pull-right"
				    type="text"
				    id="publishedOnUI"
				    v-placeholder="Publish On">

			    <input
				    type="hidden"
				    name="publishedOn"
				    id="publishedOn"
				    v-model="post.publishedOn">
		    </td>
	    </tr>
	    </tbody>
    </table>

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
<link href="vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script>
	var data = datas[0],
        publishedOn = app.getElementById('publishedOn'),
		publishedOnUI = app.getElementById('publishedOnUI');

	$(publishedOnUI)
		.datepicker()
		.on('changeDate', function(e) {
			data.post.publishedOn =
			publishedOn.value = Math.floor(e.date / 1000);
		});
</script>