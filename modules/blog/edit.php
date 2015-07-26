<?php
use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Blog\Post;
use Enpowi\Modules\Module;

Module::is();

$name = App::param('name');

$data = (new DataOut())
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
		<a href="https://github.com/wikiLingo/wikiLingo/wiki/Expressions-%28aka-%22wiki-syntax%22%29" target="_blank">
			<span v-title="syntax help" class="glyphicon glyphicon-info-sign pull-right" aria-hidden="true"></span>
		</a>
    </h3>

    <div class="wide fixed">
	    <label>
		    <span v-t>Post Name</span>
		    <input
			    class="form-control"
			    type="text"
			    name="name"
			    v-model="post.name"
			    v-placeholder="Post Name">
	    </label>
	    <label>
		    <span v-t>Publish On</span>
		    <span v-title="blank for immediate" class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
		    <input
			    class="form-control pull-right"
			    type="text"
			    id="publishedOnUI"
			    v-placeholder="Publish On"
			    autocomplete="off">
	    </label>
	    <input
		    type="hidden"
		    name="publishedOn"
		    id="publishedOn"
		    v-model="post.publishedOn">
    </div>
	<pre>{{ stringify(post) }}</pre>
    <div class="form-group">
        <input type="hidden" name="post" v-model=" stringify(post) ">
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
		post = data.post,
        publishedOn = app.getElementById('publishedOn'),
		publishedOnUI = app.getElementById('publishedOnUI'),
		$publishedOnUI = $(publishedOnUI)
			.datepicker()
			.on('change', function() {
				console.log(this.value);
				if (!this.value) {
					post.publishedOn = '';
				}
			})
			.on('changeDate', function(e) {
				post.publishedOn = Math.floor(e.date / 1000);
			});

	if (post.publishedOn) {
		$publishedOnUI.datepicker('setDate', moment(post.publishedOn * 1000).toDate())
	}
</script>