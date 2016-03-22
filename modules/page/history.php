<?php
use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Pages\Page;
use Enpowi\Modules\Module;

Module::is();

$name = App::param('name');

(new DataOut)
	->add('name', $name)
  ->add('left', 0)
  ->add('right', 0)
	->add('history', (new Page($name))->history())
	->bind();

?><form
	v-module
	class="container"
  action="page/compare?name={{ name }}&left={{ left }}&right={{ right }}">

    <input type="hidden" value="{{ name }}" name="name">

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
		<tr v-for="page in history">
			<td>{{ page.created }}</td>
			<td>{{ page.createdBy }}</td>
			<td class="center"><input type="radio" name="left" value="{{ page.id }}" v-model="left"></td>
			<td class="center"><input type="radio" name="right" value="{{ page.id }}" v-model="right"></td>
		</tr>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="2"></td>
			<td colspan="2" class="center"><button v-t type="submit" class="btn btn-success">Compare</button></td>
		</tr>
		</tfoot>
	</table>
</form>