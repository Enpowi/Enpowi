<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Users\User;
use Enpowi\Users\Group;
use Enpowi\Modules\Data;

$data = new Data([
	'users' => User::users(),
	'availableGroups' => Group::groups()
]);

$data->bind();

?>
<h3 v-t>Users</h3>
<table v-module data="<?php echo $data->id ?>">
	<tbody>
		<tr>
			<th v-t>Username</th>
			<th v-t>Email</th>
			<th v-t>Created</th>
			<th v-t>Groups</th>
		</tr>
		<tr v-repeat="user : data.users">
			<td>{{ user.username }}</td>
			<td>{{ user.email }}</td>
			<td>{{ user.created }}</td>
			<td>
				<button v-on="click: go( 'group/ofUser?username=' + user.username )" v-t>Manage Groups</button>
			</td>
		</tr>
	</tbody>
</table>