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
	action="perms/editService"
	listen>

	<table>
		<thead>
			<tr>
				<th v-t colspan="2">Group: </th>
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
			<tr>
				<th v-t>Module</th>
				<th v-t>Component</th>
			</tr>
		</thead>
		<tbody>
			<tr v-repeat="components : moduleMap">
				<td>{{ $key }}</td>
				<td>
					<ul>
						<li
							v-repeat="component : components">
							{{ component }}
						</li>
					</ul>
				</td>

				<!-- Anonymous -->
				<td>
					<ul>
						<li
							v-repeat="component : components"
							v-with="module : $key">
							<input
								v-module-item
								type="checkbox"
								v-model="anonymousGroup.perms[ module + '/' + component ]"
								value="{{ anonymousGroup.name + '@' + module + '/' + component }}"
								name="perm[]"></li>
					</ul>
				</td>

				<!-- Registered -->
				<td>
					<ul>
						<li
							v-repeat="component : components"
							v-with="module : $key">
							<input
								v-module-item
								type="checkbox"
								v-model="registeredGroup.perms[ module + '/' + component ]"
								value="{{ registeredGroup.name + '@' + module + '/' + component }}"
								name="perm[]"></li>
					</ul>
				</td>

				<!-- Editable Groups -->
				<td
					v-repeat=" group : editableGroups "
					v-with="module : $key">
					<ul>
						<li
							v-repeat="component : components">
							<input
								v-module-item
								type="checkbox"
								v-model="group.perms[ module + '/' + component ]"
								value="{{ group.name + '@' + module + '/' + component }}"
								name="perm[]"></li>
					</ul>
				</td>
			</tr>
		</tbody>
	</table>

</form>
