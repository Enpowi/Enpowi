<?php
use Enpowi\Modules\Module;
use Enpowi\Modules\DataOut;
Module::is();

$data = (new DataOut)
	->add('g', 0)
	->add('name', '')
	->out();
?>
<form
	v-module
	data="<?php echo $data?>"
	class="container"
	action="gallery/createService"
	data-done="gallery?g={{ g }}">
	<h3 v-t>Create Gallery</h3>
	<table class="table">
		<tr>
			<th v-t>Name</th>
			<td>
				<input type="text" name="name" v-placeholder="Name">
				<span v-text="name"></span>
			</td>
		</tr>
		<tr>
			<th v-t>Description</th>
			<td>
				<textarea name="description" v-placeholder="Description"></textarea>
			</td>
		</tr>
	</table>
	<button v-t type="submit" class="btn btn-success">Create</button>
</form>