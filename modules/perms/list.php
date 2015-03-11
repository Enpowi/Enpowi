<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Modules\DataOut;
use Enpowi\Users\Group;
use Enpowi\Modules\Module;

$id = (new DataOut())
	->add('moduleMap', Module::map())
	->add('anonymousGroup', (new Group('Anonymous'))->updatePerms())
	->add('registeredGroup', (new Group('Registered'))->updatePerms())
	->add('editableGroups', Group::editableGroups(true, true))
	->bind();

?><form
	v-module
	data="<?php echo $id;?>"
	action="perms/listService"
	listen
	class="container">

	<table class="table">
		<thead>
			<tr>
				<th v-t colspan="2">Permission</th>
				<th v-t colspan="{{ editableGroups.length + 2 }}">Groups</th>
			</tr>
			<tr>
				<th v-t>Module</th>
				<th v-t>Component</th>
				<th>
					{{ anonymousGroup.name }}
					<input type="hidden" name="groupNames[]" value="{{ anonymousGroup.name }}">
				</th>
				<th>
					{{ registeredGroup.name }}
					<input type="hidden" name="groupNames[]" value="{{ registeredGroup.name }}">
				</th>
				<th v-repeat=" group : editableGroups ">
					{{ group.name }}
					<input type="hidden" name="groupNames[]" value="{{ group.name }}">
				</th>
			</tr>
		</thead>
		<tbody v-repeat="components : moduleMap">
			<tr v-repeat="component : components" v-with="module : $key">
				<td>{{ module }}</td>
				<td>{{ component }}</td>

				<!--Anonymous-->
				<td>
					<input
						v-module-item
						type="checkbox"
						v-model="anonymousGroup.perms[ module + '/' + component ]"
						value="{{ anonymousGroup.name + '@' + module + '/' + component }}"
						name="perm[]"
						class="form-control">
				</td>

				<!--Registered-->
				<td>
					<input
						v-module-item
						type="checkbox"
						v-model="registeredGroup.perms[ module + '/' + component ]"
						value="{{ registeredGroup.name + '@' + module + '/' + component }}"
						name="perm[]"
						class="form-control">
				</td>

				<!-- Registered -->
				<td v-repeat="group : editableGroups">
					<input
						v-module-item
						type="checkbox"
						v-model="group.perms[ module + '/' + component ]"
						value="{{ group.name + '@' + module + '/' + component }}"
						name="perm[]"
						class="form-control">
				</td>
			</tr>
		</tbody>
	</table>

</form>
