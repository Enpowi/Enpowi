<?php
use Enpowi\App;
use Enpowi\Modules\Module;

Module::is();

App::user()->logout();

echo json_encode([]);