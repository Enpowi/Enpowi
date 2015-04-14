<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Pages\Page;

$name = App::param('name');

$id = (new DataOut())
	->add('name', $name)
	->add('history', (new Page($name))->history())
	->bind();
?><div
	v-module
	data="<?php echo $id ?>"
	class="container">

	<h3><span v-t>History of </span>{{ name }}</h3>

	<table class="table table-hover click">
		<thead>
		<tr>
			<th v-t>Last Edited</th>
			<th v-t>Created By</th>
			<th></th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr v-repeat="page : history">
			<td>{{ page.created }}</td>
			<td>{{ page.createdBy }}</td>
			<td><input type="radio" name="left-compare[]"</td>
			<td><input type="radio" name="right-compare[]"</td>
		</tr>
		</tbody>
	</table>
</div>