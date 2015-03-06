<?php
if(!defined('Modular')) die('Direct access not permitted');

$replies = [];
$authentication = new Enpowi\Authentication();

//get user
$user = $authentication->getUser();
$replies['user'] = $user;


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