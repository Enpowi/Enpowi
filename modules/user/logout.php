<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Authentication;

(new Authentication())->logout();

echo json_encode([]);