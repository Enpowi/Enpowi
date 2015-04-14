<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Pages\Page;

$name = App::param('name');

$id = (new DataOut())
	->add('name', $name)
	->add('rendered', (new Page($name))->render())
	->bind();
?><div
	v-module
	data="<?php echo $id ?>"
	class="container">

	<h3>{{ name }}</h3>

	<a
		v-title="History"
		href="#/page/history?name={{ name }}"
		v-show=" hasPerm('page', 'history') "
		class="pull-right button"><span class="glyphicon glyphicon-backward"></span></a>

	<a
		v-title="Edit"
		href="#/page/edit?name={{ name }}"
		v-show=" hasPerm('page', 'edit') "
		class="pull-right button"><span class="glyphicon glyphicon-edit"></span></a>

	<div id="content">
		{{{ rendered }}}
	</div>
	<div></div>
</div>

