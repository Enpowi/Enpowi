<?php
use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Blog\Post;
use Enpowi\Modules\Module;

Module::is();
$user = App::user();

if (App::paramIs('name')) {
	$name = App::param( 'name' );
	$post = new Post($name);
	$data = (new DataOut)
		->add( 'list', false )
		->add( 'name', $name )
		->add( 'post', $post)
		->add( 'rendered', $post->render() )
		->add( 'username', $post->user()->email)
		->out();
} else {
	$page = App::paramInt('page');
	$showAll = $user->hasPerm('*', '*');
	$data = (new DataOut)
		->add( 'list', true )
		->add( 'post', [] )
		->add( 'posts', Post::posts($page, $showAll))
		->add( 'page', $page)
		->add( 'pages', Post::pages($showAll))
		->out();
}
?>
<div
    v-module
    data="<?php echo $data?>"
    class="container">

	<!-- individual post -->
	<div v-show="!list">
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

	<!-- list with "read more" -->
	<div v-show="list">
		<h3><span v-t>Blog</span>
			<a v-title="New Post" href="#/blog/edit"><span class="glyphicon glyphicon-plus-sign"></span></a></h3>
		<div v-for=" post in posts ">
			<h5>{{ post.name }} <span v-t>on</span> {{ dateFormatted(post.publishedOn) }}</h5>
			<p>
				{{ post.cacheShort }}

				<a
					href=""
					v-bind:href=" '#/blog/index?name=' + post.name "
					v-t>Read More</a>
			</p>
			<hr>
		</div>

		<nav v-pager="{
			pages: pages,
			page: page,
			url: '#/blog/index?page='
		}"></nav>
	</div>
</div>

