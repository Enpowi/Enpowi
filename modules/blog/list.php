<?php
use Enpowi\App;
use Enpowi\Blog\Post;
use Enpowi\Modules\DataOut;
use Enpowi\Modules\Module;

Module::is();
$page = App::paramInt('page');
$showAll = App::user()->hasPerm('blog', 'edit');
$data = (new DataOut())
	->add('posts', Post::posts($page, $showAll))
	->add('pages', Post::pages($showAll))
	->add('page', $page)
	->out();

?>
<title>{{session.siteName }} - Blog</title>
<div
	v-module
    data="<?php echo $data?>"
	class="container">
	<!--TODO page name-->
	<h3><span v-t>Blog Posts</span>
		<a v-title="New Post" href="#/blog/edit"><span class="glyphicon glyphicon-plus-sign"></span></a>
	</h3>
	<nav class="pull-right">
		<ul class="pagination">
			<li v-show="page > 0">
				<a href="#/" aria-label="Previous">
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<li v-show="pages.length  > 0 && page < pages[pages.length - 1]">
				<a href="#" aria-label="Next">
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
		</ul>
	</nav>

	<table class="table table-hover click">
		<thead>
		<tr>
			<th v-t>Post Name</th>
			<th v-t>Created On</th>
			<th v-t>Published On</th>
			<th v-t>Created By</th>
		</tr>
		</thead>
		<tbody>
		<tr v-repeat="post : posts" v-on="click : go('blog?name=' + post.name)">
			<td>{{ post.name }}</td>
			<td>{{ post.created }}</td>
			<td>{{ post.publishedOn }}</td>
			<td>{{ post.user.email }}</td>
		</tr>
		</tbody>
	</table>
</div>