<?php
use Enpowi\App;
use Enpowi\Users\Group;
use Enpowi\Users\User;
use Enpowi\Modules\DataOut;
use Enpowi\Modules\Module;

Module::is();
$userGroupMap = [];
$editableGroups = Group::editableGroups();
$user = new User(App::param('email'));
foreach($editableGroups as $editableGroup) {
	foreach($user->groups as $userGroup) {
		if ($editableGroup->id === $userGroup->id) {
			$userGroupMap[$userGroup->name] = true;
		}
	}

	if (empty($userGroupMap[$userGroup->id])) {
		$userGroupMap[$userGroup->name] = false;
	}
}

(new DataOut)
	->add('user', $user)
	->add('groups', $editableGroups)
	->add('userGroupMap', $userGroupMap)
	->bind();

?><form
	listen
	v-module
	action="group/ofUserService"
	class="container">

	<h3>
		<span v-t>Groups for: </span>
		{{ user.email }}
	</h3>

	<input
		name="user"
		type="hidden"
		value="{{ stringify( user ) }}">

	<div v-for=" group in groups ">
		<input
			v-module-item
			name="groups[]"
			type="checkbox"
			value="{{ stringify( group ) }}"
			v-model=" userGroupMap[group.name] ">

		<label>{{ group.name }}</label>
	</div>
</form>