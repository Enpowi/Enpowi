<?php
use Enpowi\App;
use Enpowi\Users\Group;
use Enpowi\Users\User;
use Enpowi\Modules\DataOut;
use Enpowi\Modules\Module;

Module::is();

$data = (new DataOut())
	->add('user', $user = new User(App::param('email')))
	->add('groups', Group::editableGroups())
	->out();

?><form
	listen
	v-module
    data="<?php echo $data?>"
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

	<div v-repeat=" group : groups ">

		<input
			v-module-item
			name="groups[]"
			type="checkbox"
			value="{{ stringify( group ) }}"
		    v-model=" arrayLookup(user.groups, 'id', group.id) !== null ">

		<label>{{ group.name }}</label>
	</div>
</form>