<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Users\Group;
use Enpowi\Modules\Data;

$groups = Group::groups();
$data = new Data($groups);
$id = $data->id;

echo $data->toScript();

?>
<h3 v-t>User Groups</h3>
<table v-module data="<?php echo $id?>">
	<thead>
		<tr>
			<th></th>
			<th v-t>Group Name</th>
		</tr>
	</thead>
	<tbody>
		<tr v-repeat="group : data">
			<td><input type="checkbox" name="group[]" value="{{group.name}}"></td>
			<td>{{group.name}}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">
				<select>
					<option value="" t-v>Action</option>
					<option value="delete" t-v>Delete</option>
				</select>
				<button v-t>Submit</button>
				<button v-t onclick="app.go('group/new');">New</button>
			</td>
		</tr>
	</tfoot>
</table>