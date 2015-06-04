<?php
use Enpowi\Pages\Page;
use Enpowi\Modules\DataOut;
use Enpowi\Modules\Module;

Module::is();

$data = (new DataOut())
	->add('pages', Page::pages())
	->out();

?><div
	v-module
    data="<?php echo $data?>"
	class="container">
	<!--TODO page name-->
	<h3><span v-t>Pages</span>
		<a v-title="New Page" href="#/page/edit"><span class="glyphicon glyphicon-plus-sign"></span></a></h3>

	<table class="table table-hover click">
		<thead>
		<tr>
			<th v-t>Page Name</th>
			<th v-t>Last Edited</th>
			<th v-t>Created By</th>
		</tr>
		</thead>
		<tbody>
		<tr v-repeat="page : pages" v-on="click : go('page?name=' + page.name)">
			<td>{{ page.name }}</td>
			<td>{{ page.created }}</td>
			<td>{{ page.createdBy }}</td>
		</tr>
		</tbody>
	</table>
</div>