<?php
if(!defined('Modular')) die('Direct access not permitted');
?><form v-module action="group/newService" v-module data-done="group/list">
	<h2 v-t>Create Group</h2>
	<div class="form-group">
		<input type="text" class="form-control" name="groupName" v-placeholder="Group Name">
		<span v-text="groupName"></span>
	</div>
	<button type="submit" class="btn btn-success" v-t>Submit</button>
</form>