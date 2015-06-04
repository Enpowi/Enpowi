<?php
use Enpowi\App;
use Enpowi\Users\Group;
use Enpowi\Modules\Module;

Module::is();

switch (App::param('action')) {
	case 'delete':
		foreach (App::param('groupNames') as $groupName) {
			(new Group($groupName))
				->removePerms()
				->remove();
		}
}

echo 1;