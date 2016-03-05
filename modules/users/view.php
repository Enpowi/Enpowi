<?php
use Enpowi\App;
use Enpowi\Users\User;
use Enpowi\Modules\Module;

Module::is();

$user = User::fromId(App::param('id'));

require path . '/modules/user/view.php';