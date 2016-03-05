<?php
use Enpowi\App;
use Enpowi\Modules\Module;
use Enpowi\Users\User;

Module::is();

$user = User::fromId(App::param('id'));

require path . '/modules/user/viewService.php';