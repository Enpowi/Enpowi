<?php
require_once '../module.php';

$replies = [];
$authentication = new Enpowi\Authentication();

//get user
$replies['user'] = $authentication->getUser();


//iterate through all replies and reply them as json
foreach($replies as $key => $reply) {
	$className = null;
	if ($reply !== null) {
		echo "app.updateSession('$key', " . json_encode($reply) . ");\n";
	} else {
		$className = 'Enpowi\\' . ucfirst($key);
		if (class_exists($className)) {
			echo "app.updateSession('$key', " . json_encode(get_class_vars($className)) . ");\n";
		} else {
			throw new \Exception('Unhandled type: ' . $key);
		}
	}
}