<?php
use Enpowi\App;
use Enpowi\Users\User;
use Enpowi\Users\Group;
use Enpowi\Modules\DataOut;
use Enpowi\Modules\Module;

Module::is();
$app = App::get();
$auth = $app->authentication;
$page = (App::paramIs('page') ? App::paramInt('page') : 1);
$users = null;

if (App::paramIs('email')) {
	$users = [User::getByEmail(App::param('email'))];
	$pages = 0;
} else {
	$users = User::users($page);
	$pages = User::pages();
}

$data = (new DataOut())
	->add('email', App::param('email'))
	->add('pages', $pages)
	->add('page', $page)
	->add('users', $users)
	->add('availableGroups', Group::groups())
	->add('impersonateUser', $auth->isImpersonate() ? $auth->getUser() : [])
	->add('action', '')
	->out();

?><form
	v-module
    data="<?php echo $data?>"
	action="users/listService"
	v-attr="data-done: page ? 'users/list?page=' + page : 'users/list'"
	class="container">
	<h3><span v-t>Users</span>
		<a v-title="New User" href="#/users/new"><span class="glyphicon glyphicon-plus-sign"></span></a></h3>
	<input
		name="q"
		v-placeholder="find user"
		v-find="{
			find: 'users/listService?action=find&q=',
			url: 'users/list?email='
		}"
		v-model="email">
	<table class="table">
		<tbody>
			<tr>
				<td></td>
				<th v-t>Email</th>
				<th v-t>Created</th>
				<td>
					<span v-show="impersonateUser.email">
						<span v-t>Impersonating: </span>{{ impersonateUser.email }}
					</span>
				</td>
			</tr>
			<tr v-repeat="user : users">
				<td>
					<input v-show="action !== 'impersonate'" type="checkbox" name="emails[]" value="{{ user.email }}">
					<input v-show="action === 'impersonate'" type="radio" name="impersonateUser" value="{{ user.email }}">
				</td>
				<td><a href="#/users/view?id={{ user.id }}">{{ user.email }}</td>
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
	<nav v-pager="{
		pages: pages,
		page: page,
		url: '#/users/list?page='
	}"></nav>
</form>