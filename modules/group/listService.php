<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;
use Enpowi\Users\Group;

switch (App::param('action')) {
	case 'delete':
		foreach (App::param('groupNames') as $groupName) {
			(new Group($groupName))
				->removePerms()
				->remove();
		}
}

echo 1;