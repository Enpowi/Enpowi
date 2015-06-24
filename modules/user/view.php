<?php
use Enpowi\Modules\Module;
use Enpowi\Modules\DataOut;

Module::is();

$data = (new DataOut())
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
    data-done="user/view">
	<h2 v-t>View User</h2>
	<table class="table">
		<tr>
			<th v-t>Email: </th>
			<td>{{ session.user.email }}</td>
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
        if (response !== undefined && response.passwordUpdated.length > 0) {
            password.value =
            passwordRepeat.value = '';
        }
        update.value = false;
    });
</script>