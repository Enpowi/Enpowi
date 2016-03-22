<?php
use Enpowi\Users\Group;
use Enpowi\Modules\DataOut;
use Enpowi\Modules\Module;

Module::is();

(new DataOut)
	->add('groups', Group::groups())
	->bind();

?><form
	v-module
	action="group/listService"
	data-done="group/list"
	class="container">
	<h3><span v-t>Groups</span> <a v-title="New Group" href="#group/new"><span class="glyphicon glyphicon-plus-sign"></span></a></h3>
	<table class="table">
		<tr>
			<th v-t>Group Name</th>
		</tr>
		<tr v-for="group in groups">
			<td class="checkbox" colspan="2">
				<label>
					<input
						type="checkbox"
						name="groupNames[]"
						value="{{ group.name }}"
						disabled="{{ group.isSystem }}">
					{{group.name}}
				</label>
			</td>
		</tr>
		<tr>
			<td>
				<select name="action" class="form-control inline">
					<option value="" t-v>Action</option>
					<option value="delete" t-v>Delete</option>
				</select>
				<button v-t class="btn btn-success">Submit</button>
			</td>
		</tr>
	</table>
</form>