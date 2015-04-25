<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Blog\Post;
use Enpowi\Modules\DataOut;

$data = (new DataOut())
	->add('posts', Post::posts())
	->out();

?><div
	v-module
    data="<?php echo $data?>"
	class="container">
	<!--TODO page name-->
	<h3><span v-t>Blog Posts</span>
		<a v-title="New Post" href="#/blog/edit"><span class="glyphicon glyphicon-plus-sign"></span></a></h3>

	<table class="table table-hover click">
		<thead>
		<tr>
			<th v-t>Post Name</th>
			<th v-t>Edited On</th>
			<th v-t>Created By</th>
		</tr>
		</thead>
		<tbody>
		<tr v-repeat="post : posts" v-on="click : go('blog?name=' + post.name)">
			<td>{{ post.name }}</td>
			<td>{{ post.created }}</td>
			<td>{{ post.createdBy }}</td>
		</tr>
		</tbody>
	</table>
</div>