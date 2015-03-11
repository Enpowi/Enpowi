<?php
if(!defined('Modular')) die('Direct access not permitted');
?><form
	v-module
	action="users/newService"
	data-done="users/list"
	class="container">
	<h2 v-t>New User</h2>
	<div class="form-group">
		<input type="text" class="form-control" name="username" v-placeholder="Username">
		<span v-text="username"></span>
		<input type="password" class="form-control" name="password" v-placeholder="Password">
		<span v-text="password"></span>
		<input type="text" class="form-control" name="email" v-placeholder="Email">
		<span v-text="email"></span>
	</div>
	<button type="submit" class="btn btn-success" v-t>Submit</button>
</form>