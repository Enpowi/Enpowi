<?php
use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Blog\Post;
use Enpowi\Modules\Module;

Module::is();


if (App::paramIs('name')) {
	$name = App::param( 'name' );
	$post = new Post($name);
	$data = ( new DataOut() )
		->add( 'name', $name )
		->add( 'post', $post)
		->add( 'rendered', $post->render() )
		->add( 'username', $post->user()->email)
		->out();
} else {
	$data = null;
}
?>
<div
    v-module
    data="<?php echo $data?>"
    class="container">

	<div v-show="name">
	    <h3>{{ name }}
	        <a
	            v-title="Edit"
	            href="#/blog/edit?name={{ name }}"
	            v-show=" hasPerm('blog', 'edit') "
	            class="pull-right button"><span class="glyphicon glyphicon-edit"></span></a>
	    </h3>
	    <div id="content">
	        {{{ rendered }}}
	    </div>
		<span class="pull-right help-block">
			<span v-t>Published on</span>
			<span>{{ dateFormatted(post.publishedOn) }}</span>
			<span v-t>by</span>
			<span>{{ username }}</span>
		</span>
	</div>
	<div v-show="!name">
		<h3><span v-t>Blog</span>
			<a v-title="New Post" href="#/blog/edit"><span class="glyphicon glyphicon-plus-sign"></span></a></h3>
	</div>
</div>

