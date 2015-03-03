<?php
require_once '../module.php';

use Enpowi\User;
use Enpowi\ModuleData;

$users = User::users();
$data = new ModuleData($users);
$id = $data->id;

echo $data->toScript();

?>
<h3 v-t>Users</h3>
<table v-module data="<?php echo $id?>">
	<tr>
		<th v-t>Username</th>
		<th v-t>Email</th>
		<th v-t>Created</th>
		<th></th>
	</tr>
	<tr v-repeat="user : data">
		<td>{{user.username}}</td>
		<td>{{user.email}}</td>
		<td>{{user.created}}</td>
		<td></td>
	</tr>
</table>