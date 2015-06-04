<?php
use Enpowi\App;
use Enpowi\Modules\Module;

Module::is();


$replies = [];
$authentication = new Enpowi\Authentication();

//get user
$user = $authentication->getUser()->updatePerms();
$replies['user'] = $user;
$replies['theme'] = App::$config->themeModule;


//iterate through all replies and reply them as json
foreach($replies as $key => $reply) {
	$className = null;
	if ($reply !== null) {
		echo "Enpowi.session.update('$key', " . json_encode($reply) . ");\n";
	} else {
		$className = 'Enpowi\\' . ucfirst($key);
		if (class_exists($className)) {
			echo "Enpowi.session.update('$key', " . json_encode(get_class_vars($className)) . ");\n";
		} else {
			throw new \Exception('Unhandled type: ' . $key);
		}
	}
}