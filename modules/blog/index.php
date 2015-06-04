<?php
use Enpowi\Modules\DataOut;
use Enpowi\App;
use Enpowi\Blog\Post;
use Enpowi\Modules\Module;

Module::is();

$name = App::param('name');

$data = (new DataOut())
    ->add('name', $name)
    ->add('rendered', (new Post($name))->render())
    ->out();
?><div
    v-module
    data="<?php echo $data?>"
    class="container">

    <h3>{{ name }} {{ created }}
        <a
            v-title="Edit"
            href="#/blog/edit?name={{ name }}"
            v-show=" hasPerm('blog', 'edit') "
            class="pull-right button"><span class="glyphicon glyphicon-edit"></span></a>
    </h3>
    <div id="content">
        {{{ rendered }}}
    </div>
    <div></div>
</div>

