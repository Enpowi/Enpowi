<?php
use Enpowi\Modules\DataOut;
use Enpowi\Users\Group;
use Enpowi\Modules\Module;

Module::is();

$data = (new DataOut())
	->add('moduleMap', Module::map())
	->add('anonymousGroup', (new Group('Anonymous'))->updatePerms())
	->add('registeredGroup', (new Group('Registered'))->updatePerms())
	->add('editableGroups', Group::editableGroups(true, true))
	->out();

?><form
	v-module
    data="<?php echo $data?>"
	action="perms/listService"
	listen
	class="container">

	<table class="table">
		<thead>
			<tr>
				<th v-t colspan="2" class="center">Permission</th>
				<th v-t colspan="{{ editableGroups.length + 2 }}" class="center">Groups</th>
			</tr>
			<tr>
				<th v-t>Module</th>
				<th v-t>Component</th>
				<th class="center">
					{{ anonymousGroup.name }}
					<input type="hidden" name="groupNames[]" value="{{ anonymousGroup.name }}">
				</th>
				<th class="center">
					{{ registeredGroup.name }}
					<input type="hidden" name="groupNames[]" value="{{ registeredGroup.name }}">
				</th>
				<th v-repeat=" group : editableGroups " class="center">
					{{ group.name }}
					<input type="hidden" name="groupNames[]" value="{{ group.name }}">
				</th>
			</tr>
		</thead>
		<tbody v-repeat="components : moduleMap">
			<tr v-repeat="component : components">
				<td>{{ $key }}</td>
				<td>{{ component }}</td>


				<!--Anonymous-->
				<td class="center">
                    <input
                        v-module-item
                        type="checkbox"
                        v-model="anonymousGroup.perms[ $key + '/' + component ]"
                        value="{{ anonymousGroup.name + '@' + $key + '/' + component }}"
                        name="perm[]">
				</td>

				<!--Registered-->
				<td class="center">
                    <input
                        v-module-item
                        type="checkbox"
                        v-model="registeredGroup.perms[ $key + '/' + component ]"
                        value="{{ registeredGroup.name + '@' + $key + '/' + component }}"
                        name="perm[]">
				</td>

				<!-- Registered -->
				<td v-repeat="group : editableGroups" class="center">
                    <input
                        v-module-item
                        type="checkbox"
                        v-model="group.perms[ $key + '/' + component ]"
                        value="{{ group.name + '@' + $key + '/' + component }}"
                        name="perm[]">
				</td>
			</tr>
		</tbody>
	</table>

</form>
