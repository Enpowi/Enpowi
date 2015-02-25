<?php
require_once '../module.php';

use Enpowi\Authentication;

(new Authentication())->logout();

echo json_encode([]);