<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\App;
use Enpowi\Users\Group;
use Enpowi\Users\User;
use Enpowi\Modules\Data;

$moduleData = [];
$moduleData['user'] = $user = new User(App::param('username'));
$moduleData['groups'] = Group::editableGroupsRaw();

$groupsKeyed = [];
foreach($user->groups as $group) {
	$groupsKeyed[$group->id()] = $group->name;
}
$moduleData['user']->groupsKeyed = $groupsKeyed;

$data = new Data($moduleData);

$data->bind();

?><form
	v-module
	data="<?php echo $data->id ?>"
	action="group/ofUserService"
	listener>
	<h2><span v-t>Groups for: </span>{{data.user.username}}</h2>
	<div v-repeat="group : data.groups">
		<input
			name="groups[]"
			type="checkbox"
			value="{{ group.name }}"
			checked="{{ data.user.groupsKeyed[group.id] ? true : false }}">
		<label>{{ group.name }}</label>
	</div>
</form>