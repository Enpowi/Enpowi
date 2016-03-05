<?php
use Enpowi\App;
use Enpowi\Modules\Module;
use Enpowi\Modules\DataOut;

Module::is();
if (!isset($user)) {
  $user = App::user();
}

$data = (new DataOut())
    ->add('user', $user)
    ->add('email', '')
    ->add('emailUpdated', '')
    ->add('password', '')
    ->add('passwordRepeat', '')
    ->add('passwordUpdated', '')
    ->out();

?><form
    data="<?php echo $data ?>"
	class="container"
    listen
	v-module
    action="user/viewService"
    data-done="user/view?id={{ user.id }}">
	<h2 v-t>View User</h2>
  <input type="hidden" name="id" v-model="user.id">
  <input type="hidden" name="user" v-model="stringify(user)">
	<table class="table">
		<tr>
			<th v-t>Email: </th>
			<td>
        <input type="text" v-model="user.email" v-module-item>
        <span v-text="email"></span>
      </td>
		</tr>
		<tr>
			<th v-t>Created: </th>
			<td>{{ session.user.created }}</td>
		</tr>
		<tr>
			<th v-t>Groups: </th>
			<td>
				<span v-repeat="group : session.user.groupList">{{group.name}}</span>
			</td>
		</tr>
        <tr>
            <th v-t>Change Password: </th>
            <td>
                <input type="password" name="password" id="password" v-module-item>
                <span v-text="password"></span>
            </td>
        </tr>
        <tr>
            <th v-t>Repeat Password: </th>
            <td>
                <input type="password" name="passwordRepeat" id="passwordRepeat" v-module-item>
                <span v-text="passwordRepeat"></span>
                <span v-text="passwordUpdated"></span>
            </td>
        </tr>
		<tr>
			<th v-t>Status: </th>
			<td>
				<span v-show="session.user.valid" v-t>Confirmed</span>
				<span v-show="!session.user.valid" v-t>Unconfirmed</span>
			</td>
		</tr>
        <tr>
            <td colspan="2">
                <button type="submit" id="submit">Update</button>
                <input type="hidden" id="update" name="update" value="false">
            </td>
        </tr>
	</table>
</form>
<div
	v-show='!session.user.valid'
	v-frame="'user/confirm'"></div>
<script>
    var password = app.getElementById('password'),
        passwordRepeat = app.getElementById('passwordRepeat'),
        submit = app.getElementById('submit'),
        update = app.getElementById('update');

    submit.onclick = function() {
        update.value = true;
    };

    app.subTo().listened(function(response) {
      update.value = false;
        if (response === undefined) return;
      if (response.emailUpdated && response.emailUpdated.length > 0) {
          window.location.reload();
      }
      if (response.passwordUpdated && response.passwordUpdated.length > 0) {
            password.value =
            passwordRepeat.value = '';
        }

    });
</script>