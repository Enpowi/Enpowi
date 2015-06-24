<?php
use Enpowi\App;
use Enpowi\Users\User;
use Enpowi\Users\Group;
use Enpowi\Modules\DataOut;
use Enpowi\Modules\Module;

Module::is();
$app = App::get();
$auth = $app->authentication;
$data = (new DataOut())
	->add('users', User::users())
	->add('availableGroups', Group::groups())
	->add('impersonateUser', $auth->isImpersonate() ? $auth->getUser() : null)
	->add('action', '')
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
				<th v-t>Email</th>
				<th v-t>Created</th>
				<td>
					<span v-show="impersonateUser !== null">
						<span v-t>Impersonating: </span>{{ impersonateUser.email }}
					</span>
				</td>
			</tr>
			<tr v-repeat="user : users">
				<td>
					<input v-show="action !== 'impersonate'" type="checkbox" name="emails[]" value="{{ user.email }}">
					<input v-show="action === 'impersonate'" type="radio" name="impersonateUser" value="{{ user.email }}">
				</td>
				<td>{{ user.email }}</td>
				<td>{{ user.created }}</td>
				<td>
					<a href="#/group/ofUser?email={{ user.email }}" v-t>Groups</a>
				</td>
			</tr>
			<tr>
				<td colspan="5">
					<select name="action" class="form-control inline" v-model="action">
						<option value="" t-v>Action</option>
						<option value="delete" t-v>Delete</option>
						<option value="impersonate">Impersonate</option>
					</select>
					<button v-t class="btn btn-success">Submit</button>
				</td>
			</tr>
		</tbody>
	</table>
</form>