<?php
if(!defined('Modular')) die('Direct access not permitted');

global $config;

$replies = [];
$authentication = new Enpowi\Authentication();

//get user
$user = $authentication->getUser();
$replies['user'] = $user;
$replies['theme'] = $config->themeModule;


//iterate through all replies and reply them as json
foreach($replies as $key => $reply) {
	$className = null;
	if ($reply !== null) {
		echo "Enpowi.updateSession('$key', " . json_encode($reply) . ");\n";
	} else {
		$className = 'Enpowi\\' . ucfirst($key);
		if (class_exists($className)) {
			echo "Enpowi.updateSession('$key', " . json_encode(get_class_vars($className)) . ");\n";
		} else {
			throw new \Exception('Unhandled type: ' . $key);
		}
	}
}