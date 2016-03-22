<?php
use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Pages\Page;
use Enpowi\Modules\Module;

Module::is();

$name = App::param('name');

(new DataOut)
	->add('name', $name)
	->add('rendered', (new Page($name))->render())
	->bind();
?><div
	v-module
	class="container">

	<h3>{{ name }}

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
    </h3>
	<div id="content">
		{{{ rendered }}}
	</div>
	<div></div>
</div>

