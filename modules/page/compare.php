<?php
use Enpowi\Modules\DataOut;
use Enpowi\Pages\Page;
use Enpowi\App;
use Enpowi\Modules\Module;

Module::is();

$name = App::param('name');

$data = (new DataOut())
	->add('name', $name)
	->add('leftPage', Page::byId(App::param('left')))
	->add('rightPage', Page::byId(App::param('right')))
	->out();
?><div
	v-module
    data="<?php echo $data?>"
	class="container">

	<h3><span v-t>Comparing: </span>{{ name }}</h3>

	<table class="table">
		<thead>
		<tr>
			<th class="center"><span v-t>Edited </span>{{ leftPage.created }}</th>
			<th class="center"><span v-t>Edited </span>{{ rightPage.created }}</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td><pre>{{ leftPage.content }}</pre></td>
			<td><pre>{{ rightPage.content }}</pre></td>
		</tr>
		</tbody>
		<tfoot>
		<tr>
			<td class="center"><button v-t class="btn btn-success">Revert</button></td>
			<td     class="center"><button v-t class="btn btn-success">Revert</button></td>
		</tr>
		</tfoot>
	</table>
</div>