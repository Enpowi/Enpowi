<?php
if(!defined('Modular')) die('Direct access not permitted');

use Enpowi\Users\User;
use Enpowi\Users\Group;
use Enpowi\Modules\DataOut;

$data = (new DataOut())
	->add('users', User::users())
	->add('availableGroups', Group::groups())
	->out();

?><form
	v-module
    data="<?php echo $data?>"
	action="users/listService"
	data-done="users/list"
	class="container">
	<h3><span v-t>Users</span>
		<a v-title="New User" href="#/users/new"><span class="glyphicon glyphicon-plus-sign"></span></a></h3>
	<table class="table">
		<tbody>
			<tr>
				<td></td>
				<th v-t>Username</th>
				<th v-t>Email</th>
				<th v-t>Created</th>
				<td></td>
			</tr>
			<tr v-repeat="user : users">
				<td><input type="checkbox" name="usernames[]" value="{{ user.username }}"></td>
				<td>{{ user.username }}</td>
				<td>{{ user.email }}</td>
				<td>{{ user.created }}</td>
				<td>
					<a href="#/group/ofUser?username={{ user.username }}" v-t>Groups</a>
				</td>
			</tr>
			<tr>
				<td colspan="5">
					<select name="action" class="form-control inline">
						<option value="" t-v>Action</option>
						<option value="delete" t-v>Delete</option>
					</select>
					<button v-t class="btn btn-success">Submit</button>
				</td>
			</tr>
		</tbody>
	</table>
</form>