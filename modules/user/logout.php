<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;

App::user()->logout();

echo json_encode([]);