<?php
require_once '../module.php';

use Enpowi\Group;
use Enpowi\ModuleData;

$groups = Group::groups();
$data = new ModuleData($groups);
$id = $data->id;

echo $data->toScript();

?>
<h3 v-t>User Groups</h3>
<table v-module data="<?php echo $id?>">
	<tr>
		<th v-t>Group Name</th>
	</tr>
	<tr v-repeat="group : data">
		<td>{{ group.name }}</td>
	</tr>
</table>