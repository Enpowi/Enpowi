<?php
require_once '../module.php';

$replies = [];
$authentication = new Enpowi\Authentication();
$replies['user'] = $authentication->getUser();

foreach($replies as $key => $reply) {
	if ($reply !== null) {
		echo "app.$key = " . json_encode($reply) . ";\n";
	} else {
		echo "app.$key = {};\n";
	}
}