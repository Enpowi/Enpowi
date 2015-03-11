<?php
if(!defined('Modular')) die('Direct access not permitted');
?><form
	class="container"
	v-module>
	<h2 v-t>View User</h2>
	<table class="table">
			<tr>
				<th v-t>Username: </th>
				<td>{{ session.user.username }}</td>
			</tr>
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
	</table>
</form>