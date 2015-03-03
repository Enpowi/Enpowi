<?php
require_once '../module.php';
?><br><form v-module>
	<h2 v-t>View User</h2>
	<table>
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
	</table>
</form>