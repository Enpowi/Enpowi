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
	<tr>
		<th v-t>Group Name</th>
	</tr>
	<tr v-repeat="group : data">
		<td>{{ group.name }}</td>
	</tr>
</table>